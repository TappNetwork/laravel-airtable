# Laravel SDK for Airtable

[![Latest Stable Version](https://poser.pugx.org/tapp/laravel-airtable/v/stable)](https://packagist.org/packages/tapp/laravel-airtable)
[![StyleCI](https://github.styleci.io/repos/172130876/shield?branch=master)](https://github.styleci.io/repos/172130876)
[![Quality Score](https://scrutinizer-ci.com/g/TappNetwork/laravel-airtables/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TappNetwork/laravel-airtables/?branch=master)
[![Total Downloads](https://poser.pugx.org/tapp/laravel-airtable/downloads)](https://packagist.org/packages/tapp/laravel-airtable)

A simple approach to interacting with Airtables.

## Installation

You can install the package via composer:

```bash
composer require tapp/laravel-airtable
```

Publish the config file:

```bash
php artisan vendor:publish --provider="Tapp\Airtable\AirtableServiceProvider"
```

Define airtables account information in .env:

```bash
AIRTABLE_KEY=
AIRTABLE_BASE=
AIRTABLE_TABLE=
```

* `AIRTABLE_KEY` can be retrieved here: https://airtable.com/account
* `AIRTABLE_BASE` can be found here: https://airtable.com/api, select base then copy from URL: `https://airtable.com/[Base Is Here]/api/docs#curl/introduction`
* `AIRTABLE_TABLE` can be found in the docs for the appropriate base, this is not case senstive. IE: `tasks`

## Usage

Get all records from that table.

``` php
Airtable::table('tasks')->get();
```

Get one record from the default table.

``` php
Airtable::find('id_string');
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
