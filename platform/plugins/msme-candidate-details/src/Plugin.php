<?php

namespace Impiger\MsmeCandidateDetails;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('msme_candidate_details');
    }
}
