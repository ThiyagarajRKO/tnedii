<?php

namespace Impiger\Language\Providers;

use Impiger\Language\Commands\RouteTranslationsCacheCommand;
use Impiger\Language\Commands\RouteTranslationsClearCommand;
use Impiger\Language\Commands\SyncOldDataCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (version_compare(get_cms_version(), '5.12') > 0) {
            $this->commands([
                SyncOldDataCommand::class,
                RouteTranslationsClearCommand::class,
                RouteTranslationsCacheCommand::class,
            ]);
        } else {
            $this->commands([
                SyncOldDataCommand::class,
            ]);
        }
    }
}
