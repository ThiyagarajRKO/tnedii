<?php

namespace Impiger\TrainingTitleFinancialDetail;

use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('training_title_financial_details');
    }
}
