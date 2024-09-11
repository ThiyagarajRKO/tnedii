<?php

namespace Impiger\ThemeGenerator\Providers;

use Impiger\ThemeGenerator\Commands\ThemeCreateCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeCreateCommand::class,
            ]);
        }
    }
}
