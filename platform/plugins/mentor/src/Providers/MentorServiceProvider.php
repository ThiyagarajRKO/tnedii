<?php

namespace Impiger\Mentor\Providers;

use Impiger\Mentor\Models\Mentor;
use Illuminate\Support\ServiceProvider;
use Impiger\Mentor\Repositories\Caches\MentorCacheDecorator;
use Impiger\Mentor\Repositories\Eloquent\MentorRepository;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class MentorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(MentorInterface::class, function () {
            return new MentorCacheDecorator(new MentorRepository(new Mentor));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/mentor')
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
                'id'          => 'cms-plugins-mentor',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/mentor::mentor.name',
                'icon'        => '',
                'url'         => route('mentor.index'),
                'permissions' => ['mentor.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('mentor'); 
        });
    }
}
