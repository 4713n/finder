<?php

namespace link0\Finder\Drivers;

use Illuminate\Support\Collection;
use link0\Finder\Interfaces\FinderInterface;

class RipGrepSearchDriver implements FinderInterface {
	/**
	 * @throws Exception
	 */
	public function __construct() {
		
	}

	public function search(string $query, string $path, array $options): Collection {
		// TODO
		return collect([]);
	}
}