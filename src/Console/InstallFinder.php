<?php

namespace Link000\Finder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallFinder extends Command {
	protected $signature = 'finder:install';
	protected $description = 'Install the finder package';

	public function handle() {
        $this->info('Installing Finder package...');

		$this->info('Publishing configuration...');

		if( ! $this->configExists('finder.php') ){
            $this->publishConfiguration();
            $this->info('Configuration published');
        } else {
            if( $this->shouldOverwriteConfig() ){
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Configuration already exists, skipping');
            }
        }

        $this->info('Finder installed');
    }

	private function configExists($fileName) {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig() {
        return $this->confirm('Config file already exists. Do you want to overwrite it?', false);
    }

    private function publishConfiguration($forcePublish = false) {
        $params = [
            '--provider' => "Link000\Finder\FinderServiceProvider",
            '--tag' => "config"
        ];

        if( $forcePublish === true ){
            $params['--force'] = true;
        }

       $this->call('vendor:publish', $params);
    }
}