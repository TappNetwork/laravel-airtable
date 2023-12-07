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

* `AIRTABLE_KEY` Airtable is requiring personal access tokens for Authorization starting in 2024. A token can be created here: https://airtable.com/create/tokens. If you are upgrading from an API key to access token, simply replace the value previously held in this environment variable with your new token.
* `AIRTABLE_BASE` can be found here: https://airtable.com/api, select base then copy from URL: `https://airtable.com/[Base Is Here]/api/docs#curl/introduction`
* `AIRTABLE_TABLE` can be found in the docs for the appropriate base, this is not case senstive. IE: `tasks`
* `AIRTABLE_TYPECAST` set this to true to allow automatic casting.

## Example Config

If you need to support multiple tables, add them to the tables config in the config/airtable.php
If your table is on a different base than the one set in the env, add that as well.

```
...
    'tables' => [

        'default' => [
            'name' => env('AIRTABLE_TABLE', 'Main'),
            'base' => 'base_id',
        ],

        'companies' => [
            'name' => env('AIRTABLE_COMPANY_TABLE', 'Companies'),
            'base' => 'base_id',
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

### Filter records by formula
- When using `where` is not enough you may need to pass in raw filter values.
- [Airtable reference](https://support.airtable.com/docs/formula-field-reference)
``` php
Airtable::table('tasks')->filterByFormula('OR({id} = "abc", {id} = "def", {id} = "ghi")')->get();
```

#### Sorting records

- First argument is the column name
- Second argument is the sort direction: `asc` (default) or `desc`

``` php
Airtable::orderBy('id')->get();
Airtable::orderBy('created_at', 'desc')->get();
```
You can sort by multiple fields by calling `orderBy` more than once (a single call with array syntax is not supported):
```php
Airtable::orderBy('id')->orderBy('created_at', 'desc')->get();
```

#### Set other API Parameters
``` php
Airtable::addParam('returnFieldsByFieldId', true); // one param at a time
Airtable::params(['returnFieldsByFieldId' => true, 'view' => 'My View']) // multiple params at once
```

#### Create
- Insert a record

``` php
Airtable::create(['name' => 'myName']);
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

#### Destroy
- Destroy a record

``` php
Airtable::table('companies')->destroy('rec5N7fr8GhDtdNxx');
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
