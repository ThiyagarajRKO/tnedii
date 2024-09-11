<?php

namespace Impiger\FinancialYear\Providers;

use Impiger\FinancialYear\Models\FinancialYear;
use Illuminate\Support\ServiceProvider;
use Impiger\FinancialYear\Repositories\Caches\FinancialYearCacheDecorator;
use Impiger\FinancialYear\Repositories\Eloquent\FinancialYearRepository;
use Impiger\FinancialYear\Repositories\Interfaces\FinancialYearInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class FinancialYearServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(FinancialYearInterface::class, function () {
            return new FinancialYearCacheDecorator(new FinancialYearRepository(new FinancialYear));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/financial-year')
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
                'id'          => 'cms-plugins-financial-year',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/financial-year::financial-year.name',
                'icon'        => 'fa fa-list',
                'url'         => route('financial-year.index'),
                'permissions' => ['financial-year.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('financial-year'); 
        });
    }
}
