<?php

namespace Impiger\CustomField\Facades;

use Illuminate\Support\Facades\Facade;
use Impiger\CustomField\Support\CustomFieldSupport;

class CustomFieldSupportFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CustomFieldSupport::class;
    }
}
