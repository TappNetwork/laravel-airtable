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
AIRTABLE_TYPECAST=false 
```

* `AIRTABLE_KEY` can be retrieved here: https://airtable.com/account
* `AIRTABLE_BASE` can be found here: https://airtable.com/api, select base then copy from URL: `https://airtable.com/[Base Is Here]/api/docs#curl/introduction`
* `AIRTABLE_TABLE` can be found in the docs for the appropriate base, this is not case senstive. IE: `tasks`
* `AIRTABLE_TYPECAST` set this to true to allow automatic casting.

## Example Config

If you need to support multiple tables, add them to the tables config in the config/airtable.php

```
...
    'tables' => [

        'default' => [
            'name' => env('AIRTABLE_TABLE', 'Main'),
        ],

        'companies' => [
            'name' => env('AIRTABLE_COMPANY_TABLE', 'Companies'),
        ],
        ...
    ],
...
```

## Usage

### Import the facade in your class.
```php
use Airtable;
```

#### Get records from that table
- This will only return the first 100 records due to Airtable page size limiation

``` php
Airtable::table('tasks')->get();
```

#### Get all records from that table.
- This will get all records by sending multiple requests until all record are fetched.
- Optional Parameter which is the delay between requests in microseconds as API is limited to 5 requests per second per base, defaults to 0.2 second.
``` php
Airtable::table('tasks')->all();
Airtable::table('tasks')->all(500000); // 0.5 seconds
```

#### Get one record from the default table.
``` php
Airtable::find('id_string');
```

#### Filter records
- First argument is the column name
- Second argument is the operator or the value if you want to use equal '=' as an operator.
- Third argument is the value of the filter
``` php
Airtable::where('id', '5')->get();
Airtable::where('id', '>', '5')->get();
```

#### First or Create
- First argument will be used for finding existing
- Second argument is additional data to save if no results are found and we are creating (will not be saved used if item already exists)
``` php
Airtable::firstOrCreate(['name' => 'myName'], ['field' => 'myField']);
```

#### Update or Create
- First argument will be used to find existing
- Second argument is additional data to save when we create or update
``` php
Airtable::updateOrCreate(['name' => 'myName'], ['field' => 'myField']);

Airtable::table('companies')->firstOrCreate(['Company Name' => $team->name]);
```

#### Update 
- First argument will be the id
- Second argument is the whole record including the updated fields

**Note:** Update is destructive and clear all unspecified cell values if you did not provide a value for them. use PATCH up update specified fields

``` php
Airtable::table('companies')->update('rec5N7fr8GhDtdNxx', [ 'name' => 'Google', 'country' => 'US']);
```

#### Patch
- First argument will be the id
- Second argument is the field you would like to update
``` php
Airtable::table('companies')->patch('rec5N7fr8GhDtdNxx', ['country' => 'US']);
```

#### Mass Update or Patch
- Array of data to be updated or patched

``` php
Airtable::table('companies')->patch([
    [
        'id' => 'rec5N7fr8GhDtdNxx',
        'fields' => ['country' => 'US']
    ],
    [
        'id' => 'rec8BhDt4fs2',
        'fields' => ['country' => 'UK']
    ],
    ...
]);
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
