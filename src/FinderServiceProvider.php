<?php

namespace link0\Finder;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use link0\Finder\Console\InstallFinder;
use link0\Finder\Services\FinderService;

class FinderServiceProvider extends ServiceProvider {
	/**
	 * Register
	 *
	 * @return void
	 */
	public function register() {
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'finder');

		$this->app->bind(FinderService::class, function ($app) {
			return new FinderService();
		});		
	}

	/**
	 * Boot
	 *
	 * @return void
	 */
	public function boot() {
		// register console functionalities
		if( $this->app->runningInConsole() ){
			$this->publishes([
				__DIR__.'/../config/config.php' => config_path('finder.php'),
			], 'config');
			
			$this->publishes([
                __DIR__.'/../routes/web.php' => base_path('routes/finder.php'),
            ], 'routes');

			$this->commands([
				InstallFinder::class,
			]);
		}

		$this->registerRoutes();
    }
	
	/**
	 * Register routes
	 *
	 * @return void
	 */
	protected function registerRoutes() {
		$routesFile = base_path('routes/finder.php');
		
		if( File::exists($routesFile) ){
			Route::group($this->routeConfiguration(), function() use ($routesFile) {
				$this->loadRoutesFrom($routesFile);
			});
		}
	}

	/**
	 * Routes configuration
	 *
	 * @return array
	 */
	protected function routeConfiguration(): array {
		return [
			'prefix' 		=> config('finder.route_prefix'),
			'middleware' 	=> config('finder.route_middlewares'),
		];
	}
}