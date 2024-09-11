<?php

namespace Impiger\Usergroups\Providers;

use Impiger\Usergroups\Models\Usergroups;
use Illuminate\Support\ServiceProvider;
use Impiger\Usergroups\Repositories\Caches\UsergroupsCacheDecorator;
use Impiger\Usergroups\Repositories\Eloquent\UsergroupsRepository;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupsInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
//Entity
use Impiger\Usergroups\Models\UsergroupEntity;
use Impiger\Usergroups\Repositories\Caches\UsergroupEntityCacheDecorator;
use Impiger\Usergroups\Repositories\Eloquent\UsergroupEntityRepository;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupEntityInterface;

class UsergroupsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(UsergroupsInterface::class, function () {
            return new UsergroupsCacheDecorator(new UsergroupsRepository(new Usergroups));
        });
        
        $this->app->bind(UsergroupEntityInterface::class, function () {
            return new UsergroupEntityCacheDecorator(new UsergroupEntityRepository(new UsergroupEntity));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/usergroups')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-usergroups',
                'priority'    => 3,
                'parent_id'   => 'cms-core-platform-administration',
                'name'        => 'plugins/usergroups::usergroups.name',
                'icon'        => null,
                'url'         => route('usergroups.index'),
                'permissions' => ['usergroups.index'],
            ]);
            if (is_plugin_active('crud')) {
               dashboard_menu() ->registerItem([
                'id'          => 'cms-plugins-usergroups-entity',
                'priority'    => 2,
                'parent_id'   => 'cms-plugins-crud',
                'name'        => 'plugins/usergroups::usergroups.entity_mapping.name',
                'icon'        => null,
                'url'         => route('usergroupsentity.index'),
                'permissions' => ['usergroupsentity.index'],
            ]);
            }
            
        });
    }
}
