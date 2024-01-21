<?php

namespace link0\Finder;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use link0\Finder\Console\InstallFinder;
use link0\Finder\Drivers\DriverRegistry;
use link0\Finder\Interfaces\FinderInterface;

class FinderServiceProvider extends ServiceProvider {
	/**
	 * Register
	 *
	 * @return void
	 */
	public function register() {
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'finder');

		$this->app->singleton(DriverRegistry::class, function ($app) {
            return new DriverRegistry();
        });
		
		$this->app->bind(FinderInterface::class, function ($app) {
			$driverRegistry = $app->make(DriverRegistry::class);

            // register drivers
            $this->registerDrivers($driverRegistry);

            // select active driver based on configuration
            $driverName = config('finder.driver', 'rg');
            return $driverRegistry->getDriver($driverName)();	
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

	/**
	 * Register drivers defined in config
	 *
	 * @param DriverRegistry $driverRegistry
	 * @return void
	 */
	protected function registerDrivers(DriverRegistry $driverRegistry) {
        $customDrivers = config('finder.drivers', []);
        foreach ($customDrivers as $driverName => $driverClass) {
            $driverRegistry->registerDriver($driverName, function () use ($driverClass) {
                return new $driverClass();
            });
        }
	}
}