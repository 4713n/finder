<?php

namespace Link000\Finder\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Link000\Finder\Tests\TestCase;

class InstallFinderTest extends TestCase {
	const CONFIG_FILENAME = 'finder.php';
	const INSTALL_COMMAND = 'finder:install';

	/** @test */
	public function copy_config_by_install_command(){
		// cleanup
		if( File::exists(config_path(self::CONFIG_FILENAME)) ){
			unlink(config_path(self::CONFIG_FILENAME));
		}

		$this->assertFalse(File::exists(config_path(self::CONFIG_FILENAME)));

		Artisan::call(self::INSTALL_COMMAND);

		$this->assertTrue(File::exists(config_path(self::CONFIG_FILENAME)));
	}

	/** @test */
	public function config_exists_no_overwrite_option() {
		$test_conf_content = 'test';

		File::put(config_path(self::CONFIG_FILENAME), $test_conf_content);
		
		$this->assertTrue(File::exists(config_path(self::CONFIG_FILENAME)));

		$command = $this->artisan(self::INSTALL_COMMAND);

		$command->expectsConfirmation('Config file already exists. Do you want to overwrite it?', 'no');

		$command->expectsOutput('Configuration already exists, skipping');

		$this->assertEquals($test_conf_content, file_get_contents(config_path(self::CONFIG_FILENAME)));

		// cleanup
		unlink(config_path(self::CONFIG_FILENAME));
	}

	/** @test */
	public function config_exists_overwrite_option() {
		$test_conf_content = 'test';

		File::put(config_path(self::CONFIG_FILENAME), $test_conf_content);
		
		$this->assertTrue(File::exists(config_path(self::CONFIG_FILENAME)));

		$command = $this->artisan(self::INSTALL_COMMAND);

		$command->expectsConfirmation('Config file already exists. Do you want to overwrite it?', 'yes');

		$command->execute();

		$command->expectsOutput('Overwriting configuration file...');

		$this->assertEquals(file_get_contents(__DIR__.'/../config/config.php'), file_get_contents(config_path(self::CONFIG_FILENAME)));

		// cleanup
		unlink(config_path(self::CONFIG_FILENAME));
	}
}