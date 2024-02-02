<?php

return [
	'search_base_path' 		=> base_path(),
	'route_prefix' 			=> 'finder',
	'route_middlewares' 	=> ['web'],
	'driver'				=> env('FINDER_DRIVER', 'rg'),
	'drivers'				=> [
		'rg' => Link000\Finder\Drivers\RipGrepSearchDriver::class,
	],
	'broadcasting' => [
		'method' 			=> 'websockets',
		'broadcast_name'	=> 'Link000\Finder\Events\SearchResultFoundBroadcastEvent',
		'channel_name'		=> 'finder.results',
		'channel_type'		=> 'private',
	],
];