<?php

namespace Impiger\IncubationCenter\Providers;

use Impiger\IncubationCenter\Models\IncubationCenter;
use Illuminate\Support\ServiceProvider;
use Impiger\IncubationCenter\Repositories\Caches\IncubationCenterCacheDecorator;
use Impiger\IncubationCenter\Repositories\Eloquent\IncubationCenterRepository;
use Impiger\IncubationCenter\Repositories\Interfaces\IncubationCenterInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class IncubationCenterServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(IncubationCenterInterface::class, function () {
            return new IncubationCenterCacheDecorator(new IncubationCenterRepository(new IncubationCenter));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/incubation-center')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                
                #{register_submodule_class}
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-incubation-center',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/incubation-center::incubation-center.name',
                'icon'        => 'fa fa-plus-circle',
                'url'         => route('incubation-center.index'),
                'permissions' => ['incubation-center.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('incubation-center'); 
        });
    }
}
