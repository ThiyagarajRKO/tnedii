<?php

namespace Impiger\MasterDetail\Providers;

use Impiger\MasterDetail\Models\MasterDetail;
use Illuminate\Support\ServiceProvider;
use Impiger\MasterDetail\Repositories\Caches\MasterDetailCacheDecorator;
use Impiger\MasterDetail\Repositories\Eloquent\MasterDetailRepository;
use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;
use Impiger\MasterDetail\Commands\MasterDetailSchedulerCommand;

class MasterDetailServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(MasterDetailInterface::class, function () {
            return new MasterDetailCacheDecorator(new MasterDetailRepository(new MasterDetail));
        });

        $this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\CountryInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\CountryCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\CountryRepository(new \Impiger\MasterDetail\Models\Country)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\DistrictInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\DistrictCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\DistrictRepository(new \Impiger\MasterDetail\Models\District)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\CountyInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\CountyCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\CountyRepository(new \Impiger\MasterDetail\Models\County)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\SubcountyInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\SubcountyCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\SubcountyRepository(new \Impiger\MasterDetail\Models\Subcounty)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\ParishInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\ParishCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\ParishRepository(new \Impiger\MasterDetail\Models\Parish)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\VillageInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\VillageCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\VillageRepository(new \Impiger\MasterDetail\Models\Village)
            );
        });
			
			
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\DivisionInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\DivisionCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\DivisionRepository(new \Impiger\MasterDetail\Models\Division)
            );
        });
			
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\SpecializationsInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\SpecializationsCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\SpecializationsRepository(new \Impiger\MasterDetail\Models\Specializations)
            );
        });
			
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\QualificationsInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\QualificationsCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\QualificationsRepository(new \Impiger\MasterDetail\Models\Qualifications)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\MilestoneInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\MilestoneCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\MilestoneRepository(new \Impiger\MasterDetail\Models\Milestone)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\HubTypeInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\HubTypeCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\HubTypeRepository(new \Impiger\MasterDetail\Models\HubType)
            );
        });
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\RegionInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\RegionCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\RegionRepository(new \Impiger\MasterDetail\Models\Region)
            );
        });
        
			$this->app->bind(\Impiger\MasterDetail\Repositories\Interfaces\HolidayInterface::class, function () {
            return new \Impiger\MasterDetail\Repositories\Caches\HolidayCacheDecorator(
                new \Impiger\MasterDetail\Repositories\Eloquent\HolidayRepository(new \Impiger\MasterDetail\Models\Holiday)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/master-detail')
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
                'id'          => 'cms-plugins-master-detail-main',
                'priority'    => 998,
                'parent_id'   => null,
                'name'        => 'Masters',
                'icon'        => 'fa fa-cog',
                'url'         => route('master-detail.index'),
                'permissions' => ['master-detail.index'],
            ])->registerItem([
                'id'          => 'cms-plugins-master-detail',
                'priority'    => 0,
                'parent_id'   => 'cms-plugins-master-detail-main',
                'name'        => 'plugins/master-detail::master-detail.name',
                'icon'        => '',
                'url'         => route('master-detail.index'),
                'permissions' => ['master-detail.index'],
            ]);
			
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-country',
            'priority'    => 1,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::country.name',
            'icon'        => null,
            'url'         => route('country.index'),
            'permissions' => ['country.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-district',
            'priority'    => 2,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::district.name',
            'icon'        => null,
            'url'         => route('district.index'),
            'permissions' => ['district.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-county',
            'priority'    => 3,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::county.name',
            'icon'        => null,
            'url'         => route('county.index'),
            'permissions' => ['county.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-division',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::division.name',
            'icon'        => null,
            'url'         => route('division.index'),
            'permissions' => ['division.index'],
        ]);
			
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-specializations',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::specializations.name',
            'icon'        => null,
            'url'         => route('specializations.index'),
            'permissions' => ['specializations.index'],
        ]);
			
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-qualifications',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::qualifications.name',
            'icon'        => null,
            'url'         => route('qualifications.index'),
            'permissions' => ['qualifications.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-milestone',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::milestone.name',
            'icon'        => null,
            'url'         => route('milestone.index'),
            'permissions' => ['milestone.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-hub-type',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::hub-type.name',
            'icon'        => null,
            'url'         => route('hub-type.index'),
            'permissions' => ['hub-type.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-region',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::region.name',
            'icon'        => null,
            'url'         => route('region.index'),
            'permissions' => ['region.index'],
        ]);
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-holiday',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-master-detail-main',
            'name'        => 'plugins/master-detail::holiday.name',
            'icon'        => null,
            'url'         => route('holiday.index'),
            'permissions' => ['holiday.index'],
        ]);
			#{submodule_menus}
            #{removed_submenu_items}
        });
        $this->app->register(CommandServiceProvider::class);
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            
        });
    }
}
