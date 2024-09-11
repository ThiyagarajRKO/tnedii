<?php

namespace Impiger\Theme\Facades;

use Impiger\Theme\Supports\SiteMapManager;
use Illuminate\Support\Facades\Facade;

class SiteMapManagerFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SiteMapManager::class;
    }
}
