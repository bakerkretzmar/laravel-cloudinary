<?php

namespace Bakerkretzmar\LaravelCloudinary\Facades;

use Illuminate\Support\Facades\Facade;

class Cloudinaire extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cloudinaire';
    }
}
