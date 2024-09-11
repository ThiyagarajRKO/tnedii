<?php
namespace Impiger\Workflows\Providers;
use Impiger\Workflows\Listeners\WorkflowsEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */

    protected $subscribe = [
        WorkflowsEventSubscriber::class
    ]; 
    
    public function boot()
    {
    }
}
