<?php

namespace Impiger\SeoHelper\Providers;

use Impiger\Base\Supports\Helper;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Impiger\SeoHelper\Contracts\SeoHelperContract;
use Impiger\SeoHelper\Contracts\SeoMetaContract;
use Impiger\SeoHelper\Contracts\SeoOpenGraphContract;
use Impiger\SeoHelper\Contracts\SeoTwitterContract;
use Impiger\SeoHelper\SeoHelper;
use Impiger\SeoHelper\SeoMeta;
use Impiger\SeoHelper\SeoOpenGraph;
use Impiger\SeoHelper\SeoTwitter;
use Illuminate\Support\ServiceProvider;

/**
 * @since 02/12/2015 14:09 PM
 */
class SeoHelperServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SeoMetaContract::class, SeoMeta::class);
        $this->app->bind(SeoHelperContract::class, SeoHelper::class);
        $this->app->bind(SeoOpenGraphContract::class, SeoOpenGraph::class);
        $this->app->bind(SeoTwitterContract::class, SeoTwitter::class);

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('packages/seo-helper')
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
