<?php

namespace Impiger\Workflows;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('workflows');
        Schema::dropIfExists('workflow_permissions');
        Schema::dropIfExists('workflow_transitions');
    }
}
