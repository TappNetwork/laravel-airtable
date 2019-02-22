<?php

namespace Tapp\Airtable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tapp\LaravelAirtable\Skeleton\SkeletonClass
 */
class AirtableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'airtable';
    }
}
