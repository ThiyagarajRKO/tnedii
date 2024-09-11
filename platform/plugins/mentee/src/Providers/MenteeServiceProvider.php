<?php

namespace Impiger\Mentee\Providers;

use Impiger\Mentee\Models\Mentee;
use Illuminate\Support\ServiceProvider;
use Impiger\Mentee\Repositories\Caches\MenteeCacheDecorator;
use Impiger\Mentee\Repositories\Eloquent\MenteeRepository;
use Impiger\Mentee\Repositories\Interfaces\MenteeInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class MenteeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(MenteeInterface::class, function () {
            return new MenteeCacheDecorator(new MenteeRepository(new Mentee));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/mentee')
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
                'id'          => 'cms-plugins-mentee',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/mentee::mentee.name',
                'icon'        => '',
                'url'         => route('mentee.index'),
                'permissions' => ['mentee.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('mentee'); 
        });
    }
}
