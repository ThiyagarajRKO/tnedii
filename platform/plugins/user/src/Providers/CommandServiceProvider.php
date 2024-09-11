<?php

namespace Impiger\User\Providers;


use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $commands = \App\Utils\CrudHelper::getSchedulerCommandClass('user'); 
            if(!empty($commands)){
                $this->commands([implode(",::class",$commands)]);
            }        
            
        }
    }
}
