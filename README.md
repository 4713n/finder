## TODO

## Adding package to the project during package development
##### add to composer.json:
```json
"repositories": [
	{
		"type": "path",
		"url": "/packages/*",
		"options": {
			"symlink": false
		}
	}
]
```
this instructs composer to look for packages in the /packages
`symlink:false` will copy the package into the vendor directory, instead of creating a symlink (default behavior)

##### update composer:
```shell
composer update
```