<?php

namespace HaydarSahin\CacheMigration;

use HaydarSahin\CacheMigration\Commands\AddCacheMigrationCommand;
use HaydarSahin\CacheMigration\Commands\CacheMigratorCommand;
use Illuminate\Support\ServiceProvider;

class CacheMigrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    CacheMigratorCommand::class,
                    AddCacheMigrationCommand::class
                ]
            );
        }
    }
}
