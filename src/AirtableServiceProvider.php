<?php

namespace Tapp\Airtable;

use Illuminate\Support\ServiceProvider;

class AirtableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('airtable.php'),
            ], 'config');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'airtable');

        // Register the main class to use with the facade
        $this->app->singleton('airtable', function ($app) {
            return new AirtableManager($app);
        });

        // Register the main class to use with the facade
        $this->app->singleton('airtable.table', function () {
            return $this->app['airtable']->table($this->getDefaultTable());
        });
    }

    /**
     * Get the default file driver.
     *
     * @return string
     */
    protected function getDefaultTable()
    {
        return $this->app['config']['airtable.default'];
    }
}
