<?php

namespace Impiger\Theme\Facades;

use Impiger\Theme\ThemeOption;
use Illuminate\Support\Facades\Facade;

class ThemeOptionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     */
    protected static function getFacadeAccessor()
    {
        return ThemeOption::class;
    }
}
