<?php

namespace Impiger\InnovationVoucherProgram\Providers;

use Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram;
use Illuminate\Support\ServiceProvider;
use Impiger\InnovationVoucherProgram\Repositories\Caches\InnovationVoucherProgramCacheDecorator;
use Impiger\InnovationVoucherProgram\Repositories\Eloquent\InnovationVoucherProgramRepository;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\InnovationVoucherProgramInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class InnovationVoucherProgramServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(InnovationVoucherProgramInterface::class, function () {
            return new InnovationVoucherProgramCacheDecorator(new InnovationVoucherProgramRepository(new InnovationVoucherProgram));
        });

        $this->app->bind(\Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpCompanyDetailsInterface::class, function () {
            return new \Impiger\InnovationVoucherProgram\Repositories\Caches\IvpCompanyDetailsCacheDecorator(
                new \Impiger\InnovationVoucherProgram\Repositories\Eloquent\IvpCompanyDetailsRepository(new \Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails)
            );
        });
			$this->app->bind(\Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpKnowledgePartnerInterface::class, function () {
            return new \Impiger\InnovationVoucherProgram\Repositories\Caches\IvpKnowledgePartnerCacheDecorator(
                new \Impiger\InnovationVoucherProgram\Repositories\Eloquent\IvpKnowledgePartnerRepository(new \Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/innovation-voucher-program')
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
                'id'          => 'cms-plugins-innovation-voucher-program',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/innovation-voucher-program::innovation-voucher-program.name',
                'icon'        => 'fa fa-book-reader',
                'url'         => route('innovation-voucher-program.index'),
                'permissions' => ['innovation-voucher-program.index'],
            ]);
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-innovation-voucher-program_1',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-innovation-voucher-program',
            'name'        => 'plugins/innovation-voucher-program::innovation-voucher-program.name',
            'icon'        => null,
            'url'         => route('innovation-voucher-program.index'),
            'permissions' => ['innovation-voucher-program.index'],
        ]);
			
            
			
			#{submodule_menus}
            
			
			dashboard_menu()->removeItem('cms-plugins-ivp-company-details','cms-plugins-innovation-voucher-program');
			
			dashboard_menu()->removeItem('cms-plugins-ivp-knowledge-partner','cms-plugins-innovation-voucher-program');
			#{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('innovation-voucher-program'); 
        });
    }
}
