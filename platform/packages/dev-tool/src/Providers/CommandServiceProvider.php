<?php

namespace Impiger\DevTool\Providers;

use Impiger\DevTool\Commands\LocaleCreateCommand;
use Impiger\DevTool\Commands\LocaleRemoveCommand;
use Impiger\DevTool\Commands\Make\ControllerMakeCommand;
use Impiger\DevTool\Commands\Make\FormMakeCommand;
use Impiger\DevTool\Commands\Make\ModelMakeCommand;
use Impiger\DevTool\Commands\Make\RepositoryMakeCommand;
use Impiger\DevTool\Commands\Make\RequestMakeCommand;
use Impiger\DevTool\Commands\Make\RouteMakeCommand;
use Impiger\DevTool\Commands\Make\TableMakeCommand;
use Impiger\DevTool\Commands\PackageCreateCommand;
use Impiger\DevTool\Commands\PackageRemoveCommand;
use Impiger\DevTool\Commands\RebuildPermissionsCommand;
use Impiger\DevTool\Commands\TestSendMailCommand;
use Impiger\DevTool\Commands\TruncateTablesCommand;
use Impiger\DevTool\Commands\PackageMakeCrudCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TableMakeCommand::class,
                ControllerMakeCommand::class,
                RouteMakeCommand::class,
                RequestMakeCommand::class,
                FormMakeCommand::class,
                ModelMakeCommand::class,
                RepositoryMakeCommand::class,
                PackageCreateCommand::class,
                PackageMakeCrudCommand::class,
                PackageRemoveCommand::class,
                TestSendMailCommand::class,
                TruncateTablesCommand::class,
                RebuildPermissionsCommand::class,
                LocaleRemoveCommand::class,
                LocaleCreateCommand::class,
            ]);
        }
    }
}
