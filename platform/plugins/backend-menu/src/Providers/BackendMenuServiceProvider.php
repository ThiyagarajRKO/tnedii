<?php

namespace Impiger\BackendMenu\Providers;

use Impiger\BackendMenu\Models\BackendMenu;
use Impiger\BackendMenu\Facades\BackendMenuFacade;
use Illuminate\Support\ServiceProvider;
use Impiger\BackendMenu\Repositories\Caches\BackendMenuCacheDecorator;
use Impiger\BackendMenu\Repositories\Eloquent\BackendMenuRepository;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;
use Impiger\BackendMenu\Commands\BackendMenuSchedulerCommand;
use Illuminate\Foundation\AliasLoader;

class BackendMenuServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('BackendMenus', BackendMenuFacade::class);
        $this->app->bind(BackendMenuInterface::class, function () {
            return new BackendMenuCacheDecorator(new BackendMenuRepository(new BackendMenu));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/backend-menu')
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
                'id'          => 'cms-plugins-backend-menu',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/backend-menu::backend-menu.name',
                'icon'        => 'fa fa-bars',
                'url'         => route('backend-menu.getmenu'),
                'permissions' => ['backend-menu.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        $this->app->register(CommandServiceProvider::class);
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            
        });
    }
}
