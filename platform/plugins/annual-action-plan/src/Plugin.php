<?php

namespace Impiger\AnnualActionPlan;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('annual_action_plan');
    }
}
