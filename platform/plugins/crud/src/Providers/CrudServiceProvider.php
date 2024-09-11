<?php

namespace Impiger\Crud\Providers;

use App\Models\Crud;
use Illuminate\Support\ServiceProvider;
use Impiger\Crud\Repositories\Caches\CrudCacheDecorator;
use Impiger\Crud\Repositories\Eloquent\CrudRepository;
use Impiger\Crud\Repositories\Interfaces\CrudInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Console\Scheduling\Schedule;

class CrudServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(CrudInterface::class, function () {
            return new CrudCacheDecorator(new CrudRepository(new Crud));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }
    /*
     * @customized sabari shankar parthiban
     */
    public function boot()
    {
        $this->setNamespace('plugins/crud')
            ->loadAndPublishConfigurations(['permissions','general'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            // if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            //     \Language::registerModule([Crud::class]);
            // }
            if(SHOW_CRUD_GENERATOR_MENU){
                dashboard_menu()->registerItem([
                    'id'          => 'cms-plugins-crud',
                    'priority'    => 997,
                    'parent_id'   => null,
                    'name'        => 'plugins/crud::crud.name',
                    'icon'        => 'fa fa-list',
                    'url'         => route('crud.index'),
                    'permissions' => ['crud.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-crud-generator',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-crud',
                    'name'        => 'plugins/crud::crud.crud_generator',
                    'icon'        => null,
                    'url'         => route('crud.index'),
                    'permissions' => ['crud.index'],
                ]);
            }
        });
        $this->app->register(CommandServiceProvider::class);
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);

            $schedule = $this->app->make(Schedule::class);


        });
    }
}
