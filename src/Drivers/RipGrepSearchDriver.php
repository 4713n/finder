<?php

namespace link0\Finder\Drivers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use link0\Finder\DTO\SearchResultDTO;
use Illuminate\Support\Facades\Process;
use link0\Finder\Interfaces\FinderInterface;
use link0\Finder\Events\SearchResultFoundEvent;
use link0\Finder\Interfaces\SearchResultsInterface;

class RipGrepSearchDriver implements FinderInterface {

	private int $resultsLimit;
	private int $lineLengthLimit;
	private string $resultsTempFile;
	private int $searchTimeout;

	/**
	 * @throws Exception
	 */
	public function __construct() {
		$this->resultsLimit = 10000;
		$this->lineLengthLimit = 500;
		$this->resultsTempFile = tempnam("/tmp", "finder.");
		$this->searchTimeout = 60*3;
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
						'lI' . 
						(isset($options['useRegex']) && $options['useRegex'] ? 'Pe' : 'F') . 
						(isset($options['filesWithoutMatch']) && $options['filesWithoutMatch'] ? ' --files-without-match' : '');
		
		$grep_cmd = "rg -j10 -{$grep_options} {$this->mb_escapeshellarg($query)}";

		$search_cmd = "{$find_cmd} | xargs -0 -n 500 {$grep_cmd} | tee " . escapeshellarg($this->resultsTempFile);

		// start search
		$startTime = microtime(true);
		
		$resultsProcessed = 0;
		Process::path($workingDir)
				->timeout($this->searchTimeout)
				->quietly()
				->run($search_cmd, function(string $type, string $result) use (&$resultsProcessed) {
					if( $type !== 'out' || empty($result) || $resultsProcessed > $this->resultsLimit ) return;

					foreach(explode(PHP_EOL, $result) as $resultLine){
						if( empty($resultLine) ) continue;
						event(new SearchResultFoundEvent('info', $resultLine));
						$resultsProcessed++;
					}
				});
		
		$searchResults->setDuration(microtime(true) - $startTime);

		// read results
		$results_cmd = "head -{$this->resultsLimit} " . escapeshellarg($this->resultsTempFile) . " | cut -c 1-{$this->lineLengthLimit}";
		
		$process = Process::timeout($this->searchTimeout)->run($results_cmd);
		
		$searchResults->setResults($process->output());
		
		// number of total results
		$total_cmd = 'wc -l < ' . escapeshellarg($this->resultsTempFile);

		$process = Process::timeout($this->searchTimeout)->run($total_cmd);
		
		$searchResults->setTotal(intval(trim($process->output())));

		// TODO: remove
		$searchResults->setAdditionalData([
			'cmd' => $search_cmd,
		]);

		if( is_file($this->resultsTempFile) ) unlink($this->resultsTempFile);

		return $searchResults;
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
		$result = '\( ';
		foreach($extensionsList as $i => $extension){
			$result .= '-iname ' . escapeshellarg("{$filename}.{$extension}") . ($i+1 < count($extensions) ? '-o ' : '');
		}
		$result .= ' \)';

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
}