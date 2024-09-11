<?php

namespace Impiger\TnsiStartup\Providers;

use Impiger\TnsiStartup\Models\TnsiStartup;
use Illuminate\Support\ServiceProvider;
use Impiger\TnsiStartup\Repositories\Caches\TnsiStartupCacheDecorator;
use Impiger\TnsiStartup\Repositories\Eloquent\TnsiStartupRepository;
use Impiger\TnsiStartup\Repositories\Interfaces\TnsiStartupInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class TnsiStartupServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(TnsiStartupInterface::class, function () {
            return new TnsiStartupCacheDecorator(new TnsiStartupRepository(new TnsiStartup));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/tnsi-startup')
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
                'id'          => 'cms-plugins-tnsi-startup',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/tnsi-startup::tnsi-startup.name',
                'icon'        => 'fas fa-chalkboard-teacher',
                'url'         => route('tnsi-startup.index'),
                'permissions' => ['tnsi-startup.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('tnsi-startup'); 
        });
    }
}
