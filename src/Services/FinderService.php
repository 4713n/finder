<?php

namespace link0\Finder\Services;

use Illuminate\Support\Collection;
use link0\Finder\Interfaces\FinderInterface;

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
	public function search(string $query, string $path, array $options): Collection {
		return $this->driver->search($query, $path, $options);
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