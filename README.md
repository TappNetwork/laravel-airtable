# Laravel SDK for Airtable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/laravel-airtable.svg?style=flat-square)](https://packagist.org/packages/tapp/laravel-airtable)
[![Build Status](https://img.shields.io/travis/tapp/laravel-airtable/master.svg?style=flat-square)](https://travis-ci.org/tapp/laravel-airtable)
[![StyleCI](https://github.styleci.io/repos/172130876/shield?branch=master)](https://github.styleci.io/repos/172130876)

[![Quality Score](https://img.shields.io/scrutinizer/g/tapp/laravel-airtable.svg?style=flat-square)](https://scrutinizer-ci.com/g/tapp/laravel-airtable)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/laravel-airtable.svg?style=flat-square)](https://packagist.org/packages/tapp/laravel-airtable)

A simple approach to interacting with Airtables.

## Installation

You can install the package via composer:

```bash
composer require tapp/laravel-airtable
```

## Usage

``` php
Airtable::table('tasks')->get();
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email steve@tappnetwork.com instead of using the issue tracker.

## Credits

- [Steve Williamson](https://github.com/tapp)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
