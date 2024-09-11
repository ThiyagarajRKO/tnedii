<?php

namespace Impiger\MsmeCandidateDetails\Providers;

use Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails;
use Illuminate\Support\ServiceProvider;
use Impiger\MsmeCandidateDetails\Repositories\Caches\MsmeCandidateDetailsCacheDecorator;
use Impiger\MsmeCandidateDetails\Repositories\Eloquent\MsmeCandidateDetailsRepository;
use Impiger\MsmeCandidateDetails\Repositories\Interfaces\MsmeCandidateDetailsInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class MsmeCandidateDetailsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(MsmeCandidateDetailsInterface::class, function () {
            return new MsmeCandidateDetailsCacheDecorator(new MsmeCandidateDetailsRepository(new MsmeCandidateDetails));
        });

        #{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/msme-candidate-details')
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
                'id'          => 'cms-plugins-msme-candidate-details',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/msme-candidate-details::msme-candidate-details.name',
                'icon'        => 'fa fa-list',
                'url'         => route('msme-candidate-details.index'),
                'permissions' => ['msme-candidate-details.index'],
            ]);
            #{mainmodule_menu}
            #{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('msme-candidate-details'); 
        });
    }
}
