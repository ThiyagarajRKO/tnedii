<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;
/* @Customized By Sabari Shankar parthiban start*/
use Impiger\Base\Supports\Helper;
/* @Customized By Sabari Shankar parthiban end*/

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /* @Customized By Sabari Shankar parthiban start*/
        Helper::autoload(__DIR__ . '/../helpers');
        /* @Customized By Sabari Shankar parthiban end*/
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        /* @Customized By Sabari Shankar parthiban start*/
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
        /* @Customized By Sabari Shankar parthiban end*/
    }
}
