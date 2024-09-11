<?php

namespace Impiger\Base\Providers;

use Impiger\Base\Commands\ClearLogCommand;
use Impiger\Base\Commands\InstallCommand;
use Impiger\Base\Commands\PublishAssetsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ClearLogCommand::class,
            InstallCommand::class,
            PublishAssetsCommand::class,
        ]);
    }
}
