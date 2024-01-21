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
| Config name 			|  Default value  |
|:----------------------|:---------------:|
| search_base_path   	| `base_path()`	  |


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