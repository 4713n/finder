## Configuration

### 1. using configuration file
#### publish the finder configuration by running
```shell
php artisan vendor:publish --provider="link0\Finder\FinderServiceProvider" --tag="config"
```
and adjust settings in the `config/finder.php` file

### 2. runtime configuration
#### get config value
`config('finder.search_base_path')`
#### set config value
`config('finder.search_base_path', '/my/custom/path')`

---

### Configs
Options applied only on routes published in default `routes/finder.php` file
| Config name 			|  Default value |
|:----------------------|:--------------:|
| route_prefix   		| `finder` 		 |
| route_middlewares   	|  `['web']`  	 |

Other options
| Config name 			|  Default value  												| Description 				|
|:----------------------|:--------------------------------------------------------------|---------------------------|
| search_base_path   	| `base_path()`	  												| Where to search 			|
| driver   				| `env('FINDER_DRIVER', 'rg')`									| Active search driver 		|
| drivers   			| `['rg' => link0\Finder\Drivers\RipGrepSearchDriver::class]` 	| Registered search drivers |

## Adding custom search driver
You can add custom search driver by implementing the `link0\Finder\Interfaces\FinderInterface`

- Create your custom driver and implement `link0\Finder\Interfaces\FinderInterface`
- Add your driver including the full namespace into the finder.drivers config
```php
// ... other configs
'drivers'				=> [
	'my_custom_driver' => App\Drivers\MyCustomSearchDriver::class,
],
```
- activate your driver
	- by changing `ENV` variable
	```shell
	FINDER_DRIVER='my_custom_driver'
	```
	- by changing `finder.driver` config
	```php
	// ... other configs
	'driver'				=> [
		'my_custom_driver' => App\Drivers\MyCustomSearchDriver::class,
	],
	```
	- or setting the driver at the runtime
	```php
	use link0\Finder\Interfaces\FinderInterface

	class YourController {
    	protected FinderInterface $finderService;

		public function __construct(FinderInterface $finderService) {
			$this->finderService = $finderService;
		}

		public function search() {
			// use your driver dynamically
			$this->finderService->setDriver(app(MyCustomSearchDriver::class));
		}
	}
	```

## Installing pre-defined stacks
This installs pre-defined stack with routes, controllers, services, styles etc.

I **recommend** to have a **clean git working tree**, so you can see newly created files

You should customize or remove unnecessary to fit your current application

#### Available stacks
- `vue` (vue + inertia, tailwind)
- `blade` (blade, tailwind)
- `api` (routes only)
```shell
php artisan finder:install
```

## Notes for package development
#### Adding package to the project during package development
add to your application composer.json:
```json
"repositories": [
	{
		"type": "path",
		"url": "/packages/*",
		"options": {
			"symlink": true
		}
	}
]
```
This instructs composer to look for packages in the /packages
and creates a symlink to the installed package

`symlink:false` will copy the package into the vendor directory, instead of creating a symlink (default behavior)

#### update composer:
```shell
composer update
```