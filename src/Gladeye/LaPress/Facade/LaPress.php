<?php namespace Gladeye\LaPress\Facade;

use Illuminate\Support\Facades\Facade;

class LaPress extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'la-press'; }
}
