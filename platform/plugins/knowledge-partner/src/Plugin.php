<?php

namespace Impiger\KnowledgePartner;

use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;
use Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('knowledge-partners');
    }
}
