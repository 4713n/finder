<?php

namespace Link000\Finder\Tests;

use Link000\Finder\FinderServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase {
	public function setUp(): void {
		parent::setUp();
	}

	protected function getPackageProviders($app) {
		return [
			FinderServiceProvider::class
		];
	}

	protected function getEnvironmentSetUp($app) {

	}
}
