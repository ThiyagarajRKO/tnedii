<?php

namespace Impiger\BackendMenu\Providers;

use Impiger\BackendMenu\Commands\BackendMenuSchedulerCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackendMenuSchedulerCommand::class,
            ]);
        }
    }
}
