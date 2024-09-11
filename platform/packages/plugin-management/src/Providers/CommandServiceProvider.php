<?php

namespace Impiger\PluginManagement\Providers;

use Impiger\PluginManagement\Commands\PluginActivateAllCommand;
use Impiger\PluginManagement\Commands\PluginActivateCommand;
use Impiger\PluginManagement\Commands\PluginAssetsPublishCommand;
use Impiger\PluginManagement\Commands\PluginDeactivateCommand;
use Impiger\PluginManagement\Commands\PluginRemoveCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PluginAssetsPublishCommand::class,
            ]);
        }

        $this->commands([
            PluginActivateCommand::class,
            PluginDeactivateCommand::class,
            PluginRemoveCommand::class,
            PluginActivateAllCommand::class,
        ]);
    }
}
