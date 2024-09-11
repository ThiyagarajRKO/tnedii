<?php

namespace Impiger\BackendMenu\Facades;

use Impiger\BackendMenu\BackendMenu;
use Illuminate\Support\Facades\Facade;

class BackendMenuFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     */
    protected static function getFacadeAccessor()
    {
        return BackendMenu::class;
    }
}
