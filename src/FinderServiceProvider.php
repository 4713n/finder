<?php

namespace Link000\Finder;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Link000\Finder\Console\InstallFinder;
use Link000\Finder\Drivers\DriverRegistry;
use Link000\Finder\Interfaces\FinderInterface;
use Link000\Finder\Providers\EventServiceProvider;
use Link000\Finder\Providers\BroadcastServiceProvider;

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

		// register events
		$this->app->register(EventServiceProvider::class);

		// register channels
		$this->app->register(BroadcastServiceProvider::class);
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
                __DIR__.'/../routes/channels.php' => base_path('routes/channels_finder.php'),
            ], 'routes');

			$this->publishes([
                __DIR__.'/../Channels/FinderChannel.php' => base_path('app/Channels/FinderChannel.php'),
            ], 'channels');

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