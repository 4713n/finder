<?php

namespace link0\Finder\Tests;

use link0\Finder\FinderServiceProvider;

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
