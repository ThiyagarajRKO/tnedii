<?php

namespace Impiger\Crud\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider {

    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
            ]);
        }
    }

}
