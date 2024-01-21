<?php

return [
	'search_base_path' 		=> base_path(),
	'route_prefix' 			=> 'finder',
	'route_middlewares' 	=> ['web'],
	'driver'				=> env('FINDER_DRIVER', 'rg'),
	'drivers'				=> [
		'rg' => link0\Finder\Drivers\RipGrepSearchDriver::class,
	],
];