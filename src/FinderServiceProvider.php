<?php

namespace link0\Finder;

use Illuminate\Support\ServiceProvider;
use link0\Finder\Console\InstallFinder;

class FinderServiceProvider extends ServiceProvider {
	public function register() {
		
	}

	public function boot() {
		// register install command
		if( $this->app->runningInConsole() ){
			$this->commands([
				InstallFinder::class,
			]);
		}
    }
}