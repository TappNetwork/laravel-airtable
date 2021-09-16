<?php

namespace Tapp\Airtable\Tests;

use Illuminate\Support\Facades\Config;
use Tapp\Airtable\AirtableServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup

        Config::set('airtable.tables.companies', [
            'name' => 'Companies',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            AirtableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
