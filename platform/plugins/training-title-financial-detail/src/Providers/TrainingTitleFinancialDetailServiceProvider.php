<?php

namespace Impiger\TrainingTitleFinancialDetail\Providers;

use Impiger\TrainingTitleFinancialDetail\Models\TrainingTitleFinancialDetail;
use Illuminate\Support\ServiceProvider;
use Impiger\TrainingTitleFinancialDetail\Repositories\Caches\TrainingTitleFinancialDetailCacheDecorator;
use Impiger\TrainingTitleFinancialDetail\Repositories\Eloquent\TrainingTitleFinancialDetailRepository;
use Impiger\TrainingTitleFinancialDetail\Repositories\Interfaces\TrainingTitleFinancialDetailInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class TrainingTitleFinancialDetailServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(TrainingTitleFinancialDetailInterface::class, function () {
            return new TrainingTitleFinancialDetailCacheDecorator(new TrainingTitleFinancialDetailRepository(new TrainingTitleFinancialDetail));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/training-title-financial-detail')
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
                'id'          => 'cms-plugins-training-title-financial-detail',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/training-title-financial-detail::training-title-financial-detail.name',
                'icon'        => 'fa fa-money',
                'url'         => route('training-title-financial-detail.index'),
                'permissions' => ['training-title-financial-detail.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('training-title-financial-detail'); 
        });
    }
}
