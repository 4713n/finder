<?php

namespace Link000\Finder\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void {
		Broadcast::routes();

		$this->registerChannels();
	}

	private function registerChannels(){
		$channelsFile = base_path('routes/channels_finder.php');
		
		if( File::exists($channelsFile) ){
			require $channelsFile;
		}
	}
}