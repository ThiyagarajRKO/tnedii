<?php

namespace Impiger\Attendance\Providers;

use Impiger\Attendance\Models\Attendance;
use Illuminate\Support\ServiceProvider;
use Impiger\Attendance\Repositories\Caches\AttendanceCacheDecorator;
use Impiger\Attendance\Repositories\Eloquent\AttendanceRepository;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class AttendanceServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AttendanceInterface::class, function () {
            return new AttendanceCacheDecorator(new AttendanceRepository(new Attendance));
        });

        $this->app->bind(\Impiger\Attendance\Repositories\Interfaces\AttendanceRemarksInterface::class, function () {
            return new \Impiger\Attendance\Repositories\Caches\AttendanceRemarksCacheDecorator(
                new \Impiger\Attendance\Repositories\Eloquent\AttendanceRemarksRepository(new \Impiger\Attendance\Models\AttendanceRemarks)
            );
        });
			$this->app->bind(\Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface::class, function () {
            return new \Impiger\Attendance\Repositories\Caches\AttendanceRemarkCacheDecorator(
                new \Impiger\Attendance\Repositories\Eloquent\AttendanceRemarkRepository(new \Impiger\Attendance\Models\AttendanceRemark)
            );
        });
			$this->app->bind(\Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface::class, function () {
            return new \Impiger\Attendance\Repositories\Caches\AttendanceRemarkCacheDecorator(
                new \Impiger\Attendance\Repositories\Eloquent\AttendanceRemarkRepository(new \Impiger\Attendance\Models\AttendanceRemark)
            );
        });
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/attendance')
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
                'id'          => 'cms-plugins-attendance',
                'priority'    => 3,
                'parent_id'   => null,
                'name'        => 'plugins/attendance::attendance.name',
                'icon'        => 'fa fa-clock',
                'url'         => route('attendance.index'),
                'permissions' => ['attendance.index'],
            ])
            ->registerItem([
                'id'          => 'cms-plugins-mark-attendance',
                'priority'    => 3,
                'parent_id'   => 'cms-plugins-attendance',
                'name'        => 'plugins/attendance::attendance.name',
                'icon'        => '',
                'url'         => route('attendance.index'),
                'permissions' => ['attendance.inline_edit'],
            ])
            ->registerItem([
                'id'          => 'cms-plugins-view-attendance',
                'priority'    => 3,
                'parent_id'   => 'cms-plugins-attendance',
                'name'        => 'plugins/attendance::attendance.view',
                'icon'        => '',
                'url'         => route('attendance.view'),
                'permissions' => ['attendance.view'],
            ]);
            
			
           
			dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-attendance-remark',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-attendance',
            'name'        => 'plugins/attendance::attendance-remark.name',
            'icon'        => null,
            'url'         => route('attendance-remark.index'),
            'permissions' => ['attendance-remark.index'],
        ]);
			
			#{submodule_menus}
            #{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('attendance'); 
        });
    }
}
