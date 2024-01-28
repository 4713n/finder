<?php

namespace link0\Finder\Drivers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use link0\Finder\DTO\SearchResultDTO;
use Symfony\Component\Process\Process;
use link0\Finder\Events\SearchStartedEvent;
use link0\Finder\Interfaces\FinderInterface;
use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Interfaces\SearchResultsInterface;

class RipGrepSearchDriver implements FinderInterface {

	private int $resultsLimit;
	private int $lineLengthLimit;
	private string $resultsTempFile;
	private int $searchTimeout;
	private string $activeSearchKeyPrefix;

	/**
	 * @throws Exception
	 */
	public function __construct() {
		$this->resultsLimit = 10000;
		$this->lineLengthLimit = 500;
		$this->resultsTempFile = tempnam("/tmp", "finder.");
		$this->searchTimeout = 60*3;
		$this->activeSearchKeyPrefix = 'link0.finder.search.';
	}

	public function search(string $query, string $path, array $options): SearchResultsInterface {
		$workingDir = config('finder.search_base_path') . $path;

		$searchResults = new SearchResultDTO;
		$searchResults->setDuration(0);
		$searchResults->setTotal(0);
		$searchResults->setPath($workingDir);
		$searchResults->setResults([]);
		
		// construct find
		$find_iname = $this->getFindFilesFilterParam($options['filenamePattern'] ?? '*', $options['extensionPatterns'] ?? ['*']); 
		$find_timeFilter = $this->getFindMtimeFilterParam($options['newerThan'] ?? null, $options['olderThan'] ?? null);
		$find_includePaths = $this->getFindIncludePathsParam($options['includedPaths'] ?? []);
		$find_excludePaths = $this->getFindExcludePathsParam($options['excludedPaths'] ?? []);

		$find_cmd = "export LC_ALL=C; find . -type f {$find_iname} {$find_includePaths} {$find_excludePaths} {$find_timeFilter} -print0 2>/dev/null";

		// construct grep
		$grep_options = (isset($options['caseSensitive']) && $options['caseSensitive'] ? '' : 'i') . 
						(isset($options['useRegex']) && $options['useRegex'] ? 'Pe' : 'F') . 
						(isset($options['filesWithoutMatch']) && $options['filesWithoutMatch'] ? ' --files-without-match' : '');
		
		$grep_cmd = "rg -j10 -lc -{$grep_options} {$this->mb_escapeshellarg($query)}";

		$search_cmd = "cd " . escapeshellarg($workingDir) . " && {$find_cmd} | xargs --no-run-if-empty -0 -n 500 {$grep_cmd} | tee " . escapeshellarg($this->resultsTempFile);

		// start search
		$resultsProcessed = 0;
		$process = Process::fromShellCommandline($search_cmd, $workingDir)
				->setTimeout($this->searchTimeout)
				->disableOutput();
		
		$process->start(function(string $type, string $result) use (&$resultsProcessed) {
			if( $type !== Process::OUT || empty($result) || $resultsProcessed > $this->resultsLimit ) return;

			event(new SearchResultFoundEvent('info', '', explode(PHP_EOL, $result)));
		});

		$pid = $process->getPid();
		
		$searchId = $this->getSearchId($pid);

		$this->setActiveSearch($searchId);

		event(new SearchStartedEvent($searchId));
		
		// check every 200 ms, if search wasnt requested to stop
		while( $process->isRunning() ){
			if( ! $this->isSearchActive($searchId) ){
				$process->stop(1, 9);
				break;
			}

			usleep(200000);
		}

		$this->clearActiveSearch($searchId);

		$searchResults->setDuration(microtime(true) - $process->getStartTime());

		// read results
		$results_cmd = "head -{$this->resultsLimit} " . escapeshellarg($this->resultsTempFile) . " | cut -c 1-{$this->lineLengthLimit}";
		
		$process = Process::fromShellCommandline($results_cmd)->setTimeout($this->searchTimeout);
		$process->run();
		
		$searchResults->setResults($process->getOutput());
		
		// number of total results
		$total_cmd = 'wc -l < ' . escapeshellarg($this->resultsTempFile);

		$process = Process::fromShellCommandline($total_cmd)->setTimeout($this->searchTimeout);
		$process->run();
		
		$searchResults->setTotal(intval(trim($process->getOutput())));

		// TODO: remove
		$searchResults->setAdditionalData([
			'cmd' => $search_cmd,
		]);

		if( is_file($this->resultsTempFile) ) unlink($this->resultsTempFile);

		return $searchResults;
	}

	/**
	 * Stop search process
	 *
	 * @param string $searchId
	 * @return bool
	 */
	public function stop(string $searchId): bool {
		$this->clearActiveSearch($searchId);

		return true;
	}

	/**
	 * Sets the limit for the number of results
	 *
	 * @param int $limit
	 * @return self
	 */
	public function setResultsLimit(int $limit): self {
		$this->resultsLimit = $limit;

		return $this;
	}

	/**
	 * Sets the individual lines length limit for each result
	 *
	 * @param int $limit
	 * @return self
	 */
	public function setLineLengthLimit(int $limit): self {
		$this->lineLengthLimit = $limit;

		return $this;
	}

	/**
	 * Sets custom results temp file path
	 *
	 * @param string $path
	 * @return self
	 */
	public function setResultsTempFile(string $path): self {
		$this->resultsTempFile = $path;

		return $this;
	}

	/**
	 * Set search timeout
	 *
	 * @param integer $seconds
	 * @return self
	 */
	public function setTimeout(int $seconds): self {
		$this->searchTimeout = $seconds;

		return $this;
	}

	/**
	 * Get find files filter param
	 *
	 * @param string $filename
	 * @param array $extensions
	 * @return string
	 */
	private function getFindFilesFilterParam(string $filename, array $extensions = []): string {
		$extensionsList = array_values($extensions); // reindex possible associative array
		$result = '\(';
		foreach($extensionsList as $i => $extension){
			$result .= ' -iname ' . escapeshellarg("{$filename}.{$extension}") . ($i+1 < count($extensions) ? ' -o ' : ' ');
		}
		$result .= '\)';

		return $result;
	}

	/**
	 * Get find file mtime filter param
	 *
	 * @param string $newerThan
	 * @param string $olderThan
	 * @return string
	 */
	private function getFindMtimeFilterParam(string $newerThan = null, string $olderThan = null): string {
		$find_newer_than = $newerThan !== null ? '-newermt '.escapeshellarg($newerThan) : '';
		$find_older_than = $olderThan !== null ? '-not -newermt '.escapeshellarg($olderThan) : '';

		return "{$find_newer_than} {$find_older_than}";
	}

	/**
	 * Get find include paths param
	 *
	 * @param array $includePaths
	 * @return string
	 */
	private function getFindIncludePathsParam(array $includePaths): string {
		return implode(' ', array_map(function($includePath){ return '-path ' . escapeshellarg($includePath); }, $includePaths));
	}

	/**
	 * Get find exclude paths param
	 *
	 * @param array $excludePaths
	 * @return string
	 */
	private function getFindExcludePathsParam(array $excludePaths): string {
		return implode(' ', array_map(function($excludePath){ return '-not -path ' . escapeshellarg($excludePath); }, $excludePaths));
	}

	/**
	 * Multibyte shell arg escape
	 *
	 * @param string $arg
	 * @return string
	 */
	private function mb_escapeshellarg(string $arg): string {
		// https://stackoverflow.com/questions/1250079/how-to-escape-single-quotes-within-single-quoted-strings

		return "'" . str_replace("'", "'\"'\"'", $arg) . "'";
		//return "'" . str_replace("'", "'\\''", $arg) . "'";
	}

	/**
	 * Get search ID by search process PID
	 *
	 * @param integer|string $pid
	 * @return string
	 */
	public function getSearchId(int|string $pid): string {
		return md5(session_id() . (int)$pid);
	}

	/**
	 * Set active search by search ID
	 *
	 * @param string $searchId
	 * @param int $timeout
	 * @return void
	 */
	private function setActiveSearch(string $searchId, int $timeout = null){
		Cache::set("{$this->activeSearchKeyPrefix}.{$searchId}", true, $timeout ?? $this->searchTimeout);
	}

	/**
	 * Clear active search by search ID
	 *
	 * @param string $searchId
	 * @return void
	 */
	private function clearActiveSearch(string $searchId){
		Cache::delete("{$this->activeSearchKeyPrefix}.{$searchId}");
	}

	/**
	 * Check if search is active by search ID
	 *
	 * @param string $searchId
	 * @return void
	 */
	private function isSearchActive(string $searchId): bool {
		return Cache::get("{$this->activeSearchKeyPrefix}.{$searchId}", false) === true;
	}


}