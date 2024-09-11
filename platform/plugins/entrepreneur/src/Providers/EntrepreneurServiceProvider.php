<?php

namespace Impiger\Entrepreneur\Providers;

use Impiger\Entrepreneur\Models\Entrepreneur;
use Illuminate\Support\ServiceProvider;
use Impiger\Entrepreneur\Repositories\Caches\EntrepreneurCacheDecorator;
use Impiger\Entrepreneur\Repositories\Eloquent\EntrepreneurRepository;
use Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class EntrepreneurServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(EntrepreneurInterface::class, function () {
            return new EntrepreneurCacheDecorator(new EntrepreneurRepository(new Entrepreneur));
        });

        $this->app->bind(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class, function () {
            return new \Impiger\Entrepreneur\Repositories\Caches\TraineeCacheDecorator(
                new \Impiger\Entrepreneur\Repositories\Eloquent\TraineeRepository(new \Impiger\Entrepreneur\Models\Trainee)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/entrepreneur')
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
                'id'          => 'cms-plugins-entrepreneur',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/entrepreneur::entrepreneur.name',
                'icon'        => 'fa fa-registered',
                'url'         => route('entrepreneur.index'),
                'permissions' => ['entrepreneur.index'],
            ]);
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-entrepreneur_1',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-entrepreneur',
            'name'        => 'plugins/entrepreneur::entrepreneur.name',
            'icon'        => null,
            'url'         => route('entrepreneur.index'),
            'permissions' => ['entrepreneur.index'],
        ]);
			
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-trainee',
            'priority'    => 1,
            'parent_id'   => 'cms-plugins-entrepreneur',
            'name'        => 'plugins/entrepreneur::trainee.name',
            'icon'        => null,
            'url'         => route('trainee.index'),
            'permissions' => ['trainee.index'],
        ]);
			#{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('entrepreneur'); 
        });
    }
}
