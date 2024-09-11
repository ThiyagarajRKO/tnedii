<?php

namespace Impiger\RequestLog\Providers;

use Illuminate\Routing\Events\RouteMatched;
use Impiger\Base\Supports\Helper;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Impiger\RequestLog\Repositories\Caches\RequestLogCacheDecorator;
use Impiger\RequestLog\Repositories\Eloquent\RequestLogRepository;
use Impiger\RequestLog\Repositories\Interfaces\RequestLogInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Impiger\RequestLog\Models\RequestLog as RequestLogModel;

/**
 * @since 02/07/2016 09:50 AM
 */
class RequestLogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(RequestLogInterface::class, function () {
            return new RequestLogCacheDecorator(new RequestLogRepository(new RequestLogModel));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);

        $this->setNamespace('plugins/request-log')
            ->loadRoutes(['web'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugin-request-log',
                    'priority'    => 8,
                    'parent_id'   => 'cms-core-platform-administration',
                    'name'        => 'plugins/request-log::request-log.name',
                    'icon'        => null,
                    'url'         => route('request-log.index'),
                    'permissions' => ['request-log.index'],
                ]);
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
