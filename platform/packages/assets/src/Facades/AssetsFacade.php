<?php

namespace Impiger\Assets\Facades;

use Impiger\Assets\Assets;
use Illuminate\Support\Facades\Facade;

/**
 * Class AssetsFacade.
 *
 * @since 22/07/2015 11:25 PM
 */
class AssetsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Assets::class;
    }
}
