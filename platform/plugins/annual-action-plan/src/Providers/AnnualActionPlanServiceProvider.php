<?php

namespace Impiger\AnnualActionPlan\Providers;

use Impiger\AnnualActionPlan\Models\AnnualActionPlan;
use Illuminate\Support\ServiceProvider;
use Impiger\AnnualActionPlan\Repositories\Caches\AnnualActionPlanCacheDecorator;
use Impiger\AnnualActionPlan\Repositories\Eloquent\AnnualActionPlanRepository;
use Impiger\AnnualActionPlan\Repositories\Interfaces\AnnualActionPlanInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class AnnualActionPlanServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AnnualActionPlanInterface::class, function () {
            return new AnnualActionPlanCacheDecorator(new AnnualActionPlanRepository(new AnnualActionPlan));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/annual-action-plan')
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
                'id'          => 'cms-plugins-annual-action-plan',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/annual-action-plan::annual-action-plan.name',
                'icon'        => 'fa fa-list',
                'url'         => route('annual-action-plan.index'),
                'permissions' => ['annual-action-plan.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('annual-action-plan'); 
        });
    }
}
