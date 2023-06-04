<?php

namespace HaydarSahin\CacheMigration\Tests;

use HaydarSahin\CacheMigration\CacheMigrationServiceProvider;
use HaydarSahin\CacheMigration\Commands\CacheMigratorCommand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

abstract class CacheMigrationBase extends TestCase
{
    public $command;

    protected const TEST_DATETIME = '2023-06-01 09:00:00';

    protected function setUp(): void
    {
        parent::setUp();
        File::deleteDirectory(database_path('cache-migrations/'));
        Carbon::setTestNow(self::TEST_DATETIME);
    }

    protected function getPackageProviders($app): array
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);

        return [
            CacheMigrationServiceProvider::class,
        ];
    }

    public function createSampleMigrations()
    {
        $this->artisan('make:cache-migration sampleMigrationOne');
        $this->artisan('make:cache-migration sampleMigrationTwo');

        $this->command = new CacheMigratorCommand();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        File::deleteDirectory(database_path('cache-migrations/'));
    }
}
