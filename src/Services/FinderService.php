<?php

namespace Link000\Finder\Services;

use Illuminate\Support\Collection;
use Link000\Finder\Interfaces\FinderInterface;
use Link000\Finder\Interfaces\SearchResultsInterface;

class FinderService {
	protected FinderInterface $driver;

	/**
	 * Finder constructor.
	 *
	 * @throws Exception
	 */
	public function __construct(FinderInterface $driver) {
		$this->driver = $driver;
	}

	/**
	 * Search for files based on query, path and options
	 *
	 * @param string $query
	 * @param string $path
	 * @param array $options
	 * @return Collection
	 */
	public function search(string $query, string $path, array $options): SearchResultsInterface {
		return $this->driver->search($query, $path, $options);
	}

	/**
	 * Stop the search
	 *
	 * @param string $searchId
	 * @throws Exception
	 * @return bool
	 */
	public function stop(string $searchId): bool {
		return $this->driver->stop($searchId);
	}

	/**
	 * Get context around the match
	 *
	 * @param string $query
	 * @param string $filePath
	 * @param array $options
	 * @return 
	 */
	public function getContext(string $query, string $filePath, array $options): Collection {
		return $this->driver->getContext($query, $filePath, $options);
	}

	/**
	 * Set search driver
	 *
	 * @param FinderInterface $driver
	 * @return self
	 */
	public function setDriver(FinderInterface $driver): self {
        $this->driver = $driver;
		
		return $this;
	}
}