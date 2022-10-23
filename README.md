# This is my package filament-jet

[![Latest Version on Packagist](https://img.shields.io/packagist/v/artmin96/filament-jet.svg?style=flat-square)](https://packagist.org/packages/artmin96/filament-jet)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/artmin96/filament-jet/run-tests?label=tests)](https://github.com/artmin96/filament-jet/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/artmin96/filament-jet/Check%20&%20fix%20styling?label=code%20style)](https://github.com/artmin96/filament-jet/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/artmin96/filament-jet.svg?style=flat-square)](https://packagist.org/packages/artmin96/filament-jet)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require artmin96/filament-jet
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-jet-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-jet-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-jet-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-jet = new ArtMin96\FilamentJet();
echo $filament-jet->echoPhrase('Hello, ArtMin96!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Arthur Minasyan](https://github.com/ArtMin96)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
