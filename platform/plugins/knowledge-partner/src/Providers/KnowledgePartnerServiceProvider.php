<?php

namespace Impiger\KnowledgePartner\Providers;

use EmailHandler;
use Illuminate\Routing\Events\RouteMatched;
use Impiger\Base\Supports\Helper;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use Impiger\KnowledgePartner\Models\KnowledgePartner;
use Impiger\KnowledgePartner\Repositories\Caches\KnowledgePartnerCacheDecorator;
use Impiger\KnowledgePartner\Repositories\Eloquent\KnowledgePartnerRepository;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerReplyInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class KnowledgePartnerServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(KnowledgePartnerInterface::class, function () {
            return new KnowledgePartnerCacheDecorator(new KnowledgePartnerRepository(new KnowledgePartner));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/knowledge-partner')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes(['web'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            //->loadMigrations()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-knowledge-partner',
                'priority'    => 0,
                'parent_id'   => 'cms-plugins-innovation-voucher-program',
                'name'        => 'plugins/knowledge-partner::knowledge-partner.menu',
                'icon'        => null,
                'url'         => route('knowledge-partner.index'),
                'permissions' => ['knowledge-partner.index'],
            ]);

        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
