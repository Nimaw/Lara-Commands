# Lara-Commands
A package for more Laravel commands.

## Installation

This package can be installed via Composer:

``` bash
composer require nimaw/lara-commands --dev
```

The `Nimaw\LaraCommands\LaraCommandsServiceProvider` is auto-discovered and registered by default.

If you want to register it yourself, add the ServiceProvider in config/app.php:

```php
'providers' => [
    /*
     * Package Service Providers...
     */
    Nimaw\LaraCommands\LaraCommandsServiceProvider::class,
]

```



## Usage
After finish installation you can use command's easly.

| Command | Description |
| --- | --- |
| php artisan make:view | Create new view template |
| php artisan make:trait | Create new Trait class |
| php artisan make:service | Create new Service class |


## Credits

- [NimaW](https://github.com/niamw)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
