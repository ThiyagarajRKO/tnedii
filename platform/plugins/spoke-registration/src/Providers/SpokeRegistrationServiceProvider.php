<?php

namespace Impiger\SpokeRegistration\Providers;

use Impiger\SpokeRegistration\Models\SpokeRegistration;
use Illuminate\Support\ServiceProvider;
use Impiger\SpokeRegistration\Repositories\Caches\SpokeRegistrationCacheDecorator;
use Impiger\SpokeRegistration\Repositories\Eloquent\SpokeRegistrationRepository;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeRegistrationInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class SpokeRegistrationServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SpokeRegistrationInterface::class, function () {
            return new SpokeRegistrationCacheDecorator(new SpokeRegistrationRepository(new SpokeRegistration));
        });

        $this->app->bind(\Impiger\SpokeRegistration\Repositories\Interfaces\SpokeEcellsInterface::class, function () {
            return new \Impiger\SpokeRegistration\Repositories\Caches\SpokeEcellsCacheDecorator(
                new \Impiger\SpokeRegistration\Repositories\Eloquent\SpokeEcellsRepository(new \Impiger\SpokeRegistration\Models\SpokeEcells)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/spoke-registration')
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
                'id'          => 'cms-plugins-spoke-registration',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/spoke-registration::spoke-registration.name',
                'icon'        => 'fa fa-building',
                'url'         => route('spoke-registration.index'),
                'permissions' => ['spoke-registration.index'],
            ]);
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-spoke-registration_1',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-spoke-registration',
            'name'        => 'plugins/spoke-registration::spoke-registration.name',
            'icon'        => null,
            'url'         => route('spoke-registration.index'),
            'permissions' => ['spoke-registration.index'],
        ]);
			
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-spoke-ecells',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-spoke-registration',
            'name'        => 'plugins/spoke-registration::spoke-ecells.name',
            'icon'        => null,
            'url'         => route('spoke-ecells.index'),
            'permissions' => ['spoke-ecells.index'],
        ]);
			#{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('spoke-registration'); 
        });
    }
}
