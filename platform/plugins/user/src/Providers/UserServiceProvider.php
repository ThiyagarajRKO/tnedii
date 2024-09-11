<?php

namespace Impiger\User\Providers;

use Impiger\User\Models\User;
use Illuminate\Support\ServiceProvider;
use Impiger\User\Repositories\Caches\UserCacheDecorator;
use Impiger\User\Repositories\Eloquent\UserRepository;
use Impiger\User\Repositories\Interfaces\UserInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
/* Schedule Job */
use Illuminate\Console\Scheduling\Schedule;

class UserServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(UserInterface::class, function () {
            return new UserCacheDecorator(new UserRepository(new User));
        });

        $this->app->bind(\Impiger\User\Repositories\Interfaces\UserAddressInterface::class, function () {
            return new \Impiger\User\Repositories\Caches\UserAddressCacheDecorator(
                new \Impiger\User\Repositories\Eloquent\UserAddressRepository(new \Impiger\User\Models\UserAddress)
            );
        });
			
			
			
			#{register_sub_module}
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/user')
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
                'id'          => 'cms-plugins-user',
                'priority'    => 4,
                'parent_id'   => null,
                'name'        => 'plugins/user::user.name',
                'icon'        => 'fa fa-users',
                'url'         => route('user.index'),
                'permissions' => ['user.index'],
            ]);
            dashboard_menu()->registerItem([
            'id'          => 'cms-plugins-user_1',
            'priority'    => 0,
            'parent_id'   => 'cms-plugins-user',
            'name'        => 'plugins/user::user.name',
            'icon'        => null,
            'url'         => route('user.index'),
            'permissions' => ['user.index'],
        ]);
			
            
			
			
			
			#{submodule_menus}
            dashboard_menu()->removeItem('cms-plugins-user-address','cms-plugins-user');
			#{removed_submenu_items}
        });
        #{register_command_service}
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
            $schedule = $this->app->make(Schedule::class);

            \App\Utils\CrudHelper::callSchedulerCommandClass('user'); 
        });
    }
}
