<?php

namespace Tapp\Airtable;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Tapp\Airtable\Api\AirtableApiClient;

class AirtableManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $tables = [];

    /**
     * Create a new Airtable manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a airtable table instance.
     *
     * @param  string  $table
     */
    public function table(string $table)
    {
        return $this->resolve($table);
    }

    /**
     * Get the configuration for a table.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultTable();

        // To get the airtable table configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $tables = $this->app['config']['airtable.tables'];

        if (is_null($config = Arr::get($tables, $name))) {
            throw new InvalidArgumentException("Table [{$name}] not configured.");
        }

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultTable()
    {
        return $this->app['config']['airtable.default'];
    }

    /**
     * Set the default connection name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultTable($name)
    {
        $this->app['config']['airtable.default'] = $name;
    }

    /**
     * Resolve the given table.
     *
     * @param  string  $name
     * @return Airtable
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if ($config) {
            return $this->createAirtable($config['name'], array_key_exists('base', $config) ? $config['base'] : false);
        } else {
            throw new InvalidArgumentException("Table [{$name}] is not configured.");
        }
    }

    protected function createAirtable($table, $table_base = false)
    {
        $base = $table_base ?: $this->app['config']['airtable.base'];
        $access_token = $this->app['config']['airtable.key'];

        $airtableTypeCast = $this->app['config']['airtable.typecast'];
        $delay = $this->app['config']['airtable.delay_between_requests'];

        $client = new AirtableApiClient($base, $table, $access_token, null, $airtableTypeCast, $delay);

        return new Airtable($client);
    }

    /**
     * Get the table configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["airtable.tables.{$name}"];
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $defaultConfig = $this->app['config']['airtable.default'];
        $defaultTable = $this->app['config']["airtable.tables.{$$defaultConfig}"]['name'];

        return $this->table($defaultTable)->$method(...$parameters);
    }
}
