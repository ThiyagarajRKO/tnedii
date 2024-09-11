<?php

namespace Impiger\Mentor;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('mentors');
    }
}
