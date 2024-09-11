<?php

namespace Impiger\MasterDetail\Providers;


use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $commands = \App\Utils\CrudHelper::getSchedulerCommandClass('master-detail'); 
            if(!empty($commands)){
                $this->commands([implode(",::class",$commands)]);
            }        
            
        }
    }
}
