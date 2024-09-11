<?php

namespace Impiger\InnovationVoucherProgram;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('innovation_voucher_programs');
    }
}
