<?php

namespace Impiger\Vendor\Providers;

use Impiger\Vendor\Models\Vendor;
use Illuminate\Support\ServiceProvider;
use Impiger\Vendor\Repositories\Caches\VendorCacheDecorator;
use Impiger\Vendor\Repositories\Eloquent\VendorRepository;
use Impiger\Vendor\Repositories\Interfaces\VendorInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class VendorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(VendorInterface::class, function () {
            return new VendorCacheDecorator(new VendorRepository(new Vendor));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/vendor')
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
                'id'          => 'cms-plugins-vendor',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/vendor::vendor.name',
                'icon'        => 'fa fa-list',
                'url'         => route('vendor.index'),
                'permissions' => ['vendor.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('vendor'); 
        });
    }
}
