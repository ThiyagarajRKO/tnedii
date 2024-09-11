<?php

namespace Impiger\Workflows\Providers;

use Impiger\Workflows\Models\Workflows;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Impiger\Workflows\Repositories\Caches\WorkflowsCacheDecorator;
use Impiger\Workflows\Repositories\Eloquent\WorkflowsRepository;
use Impiger\Workflows\Repositories\Interfaces\WorkflowsInterface;
use Impiger\Base\Supports\Helper;
use Event;
use Impiger\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Foundation\AliasLoader;
use Impiger\Workflows\Facades\WorkflowsSupportFacade;
use ZeroDaHero\LaravelWorkflow\WorkflowServiceProvider;
use ZeroDaHero\LaravelWorkflow\WorkflowRegistry;
use CustomWorkflow;

class WorkflowsServiceProvider extends WorkflowServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->mergeConfigFrom(
            $this->configPath() . '/workflow_registry.php',
            'workflow_registry'
        );

        $this->commands($this->commands);
        $this->app->singleton('workflow', function ($app) {
            $workflowConfigs = config('plugins.workflows.workflow', []);
            $registryConfig = config('plugins.workflows.workflow_registry', []); 
            return new WorkflowRegistry($workflowConfigs, $registryConfig, $app->make(Dispatcher::class));
        });
        $this->app->bind(WorkflowsInterface::class, function () {
            return new WorkflowsCacheDecorator(new WorkflowsRepository(new Workflows));
        });

        $this->app->bind(\Impiger\Workflows\Repositories\Interfaces\WorkflowPermissionInterface::class, function () {    return new \Impiger\Workflows\Repositories\Caches\WorkflowPermissionCacheDecorator(
            new \Impiger\Workflows\Repositories\Eloquent\WorkflowPermissionRepository(new \Impiger\Workflows\Models\WorkflowPermission)
            );
        });

        $this->app->bind(\Impiger\Workflows\Repositories\Interfaces\WorkflowTransitionInterface::class, function () {
            return new \Impiger\Workflows\Repositories\Caches\WorkflowTransitionCacheDecorator(
                new \Impiger\Workflows\Repositories\Eloquent\WorkflowTransitionRepository(new \Impiger\Workflows\Models\WorkflowTransition)
            );
        });

        Helper::autoload(__DIR__ . '/../../helpers');
        $loader = AliasLoader::getInstance();
        $loader->alias('CustomWorkflow', WorkflowsSupportFacade::class);
    }

    public function boot()
    {   
        $configPath = $this->configPath();
        $this->publishes([
            "${configPath}/workflow.php" => $this->publishPath('workflow.php'),
            "${configPath}/workflow_registry.php" => $this->publishPath('workflow_registry.php'),
        ], 'config');
        $this->setNamespace('plugins/workflows')
            ->loadAndPublishConfigurations(['permissions', 'general', 'workflow', 'workflow_registry'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web'])
            ->publishAssets();

        

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-workflows_1',
                'parent_id'   => null,
                'name'        => 'plugins/workflows::workflows.name',
                'icon'        => 'fa fa-list',
                'url'         => route('workflows.index'),
                'permissions' => ['workflows.index'],
            ]);
        });
        
        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    protected function configPath()
    {
        return __DIR__ . '/../../config';
    }

    protected function publishPath($configFile)
    {
        return app()->basePath() . '/platform/plugins/workflows/config' . ($configFile ? '/' . $configFile : $configFile);
    }
}
