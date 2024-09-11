<?php

namespace Impiger\Workflows\Facades;

use Illuminate\Support\Facades\Facade;
use Impiger\Workflows\Support\WorkflowsSupport;

class WorkflowsSupportFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WorkflowsSupport::class;
    }
}
