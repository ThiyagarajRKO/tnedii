<?php

namespace Impiger\HubInstitution\Providers;

use Impiger\HubInstitution\Models\HubInstitution;
use Illuminate\Support\ServiceProvider;
use Impiger\HubInstitution\Repositories\Caches\HubInstitutionCacheDecorator;
use Impiger\HubInstitution\Repositories\Eloquent\HubInstitutionRepository;
use Impiger\HubInstitution\Repositories\Interfaces\HubInstitutionInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class HubInstitutionServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(HubInstitutionInterface::class, function () {
            return new HubInstitutionCacheDecorator(new HubInstitutionRepository(new HubInstitution));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/hub-institution')
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
                'id'          => 'cms-plugins-hub-institution',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/hub-institution::hub-institution.name',
                'icon'        => 'fa fa-university',
                'url'         => route('hub-institution.index'),
                'permissions' => ['hub-institution.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('hub-institution'); 
        });
    }
}
