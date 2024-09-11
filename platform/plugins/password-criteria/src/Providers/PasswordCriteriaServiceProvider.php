<?php

namespace Impiger\PasswordCriteria\Providers;

use Impiger\PasswordCriteria\Models\PasswordCriteria;
use Illuminate\Support\ServiceProvider;
use Impiger\PasswordCriteria\Repositories\Caches\PasswordCriteriaCacheDecorator;
use Impiger\PasswordCriteria\Repositories\Eloquent\PasswordCriteriaRepository;
use Impiger\PasswordCriteria\Repositories\Interfaces\PasswordCriteriaInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class PasswordCriteriaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(PasswordCriteriaInterface::class, function () {
            return new PasswordCriteriaCacheDecorator(new PasswordCriteriaRepository(new PasswordCriteria));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/password-criteria')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-password-criteria',
                'priority'    => 4,
                'parent_id'   => 'cms-core-settings',
                'name'        => 'plugins/password-criteria::password-criteria.name',
                'icon'        => null,
                'url'         => route('password-criteria.index'),
                'permissions' => ['password-criteria.index'],
            ]);
        });
        
         $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
        
    }
}
