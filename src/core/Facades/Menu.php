<?php

namespace Hairavel\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Menu';
    }
}
