# FilamentJet

[![Latest Version on Packagist](https://img.shields.io/packagist/v/artmin96/filament-jet.svg?style=flat-square)](https://packagist.org/packages/artmin96/filament-jet)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/artmin96/filament-jet/run-tests?label=tests)](https://github.com/artmin96/filament-jet/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/artmin96/filament-jet/Check%20&%20fix%20styling?label=code%20style)](https://github.com/artmin96/filament-jet/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/artmin96/filament-jet.svg?style=flat-square)](https://packagist.org/packages/artmin96/filament-jet)

![Filament Jet cover art](./art/banner.png)

> This package is basically a fusion of Jetstream and Filament Breezy with a few added features.

Filament Jet is a authentication starter kit for Filament and provides the perfect starting point for your next Filament application. Filament Jet provides the implementation for your application's login, registration, email verification, two-factor authentication, session management, personal data export, API via Laravel Sanctum, and optional team management features.

## Installation

> **Warning**
> Attempting to install Filament Jet into an existing Filament application will result in unexpected behavior and issues.

You can install the package via composer:

```bash
composer require artmin96/filament-jet
```

After installing the Filament Jet package, you may execute the following Artisan command.

```bash
php artisan filament-jet:install
```

In addition, you may use the `--teams` switch to enable team support.

After installing Filament Jet, you should migrate your database:

```bash
php artisan migrate
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

The `filament-jet` configuration file contains a features configuration array where you can enable or disable the feature you want.

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
