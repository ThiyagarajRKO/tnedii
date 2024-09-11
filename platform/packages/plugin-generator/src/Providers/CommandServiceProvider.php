<?php

namespace Impiger\PluginGenerator\Providers;

use Impiger\PluginGenerator\Commands\PluginCreateCommand;
/* @Customized By Ramesh Esakki  - Start -*/
use Impiger\PluginGenerator\Commands\PluginModuleCreateCommand;
use Impiger\PluginGenerator\Commands\PluginModuleMakeCrudCommand;
/* @Customized By Ramesh Esakki  - End -*/
use Impiger\PluginGenerator\Commands\PluginListCommand;
use Impiger\PluginGenerator\Commands\PluginMakeCrudCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
//        if ($this->app->runningInConsole()) { /* @Customized By Ramesh Esakki */
            $this->commands([
                PluginModuleCreateCommand::class, /* @Customized By Ramesh Esakki */
                PluginListCommand::class,
                PluginCreateCommand::class,
                PluginMakeCrudCommand::class,
                PluginModuleMakeCrudCommand::class /* @Customized By Ramesh Esakki */
            ]);
//        } /* @Customized By Ramesh Esakki */
    }
}
