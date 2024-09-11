<?php

namespace Impiger\TrainingTitle\Providers;

use Impiger\TrainingTitle\Models\TrainingTitle;
use Illuminate\Support\ServiceProvider;
use Impiger\TrainingTitle\Repositories\Caches\TrainingTitleCacheDecorator;
use Impiger\TrainingTitle\Repositories\Eloquent\TrainingTitleRepository;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class TrainingTitleServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(TrainingTitleInterface::class, function () {
            return new TrainingTitleCacheDecorator(new TrainingTitleRepository(new TrainingTitle));
        });

        $this->app->bind(\Impiger\TrainingTitle\Repositories\Interfaces\OnlineTrainingSessionInterface::class, function () {
            return new \Impiger\TrainingTitle\Repositories\Caches\OnlineTrainingSessionCacheDecorator(
                new \Impiger\TrainingTitle\Repositories\Eloquent\OnlineTrainingSessionRepository(new \Impiger\TrainingTitle\Models\OnlineTrainingSession)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/training-title')
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
                'id'          => 'cms-plugins-training-title',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/training-title::training-title.name',
                'icon'        => 'fa fa-list',
                'url'         => route('training-title.index'),
                'permissions' => ['training-title.index'],
            ]);
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-training-title_1',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-training-title',
            'name'        => 'plugins/training-title::training-title.name',
            'icon'        => null,
            'url'         => route('training-title.index'),
            'permissions' => ['training-title.index'],
        ]);
			
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-online-training-session',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-training-title',
            'name'        => 'plugins/training-title::online-training-session.name',
            'icon'        => null,
            'url'         => route('online-training-session.index'),
            'permissions' => ['online-training-session.index'],
        ]);
			#{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('training-title'); 
        });
    }
}
