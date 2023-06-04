<?php

namespace HaydarSahin\CacheMigration\Tests;

use HaydarSahin\CacheMigration\CacheMigrationServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

abstract class CacheMigrationBase extends TestCase
{
    protected const TEST_DATETIME = '2023-06-01 09:00:00';

    protected function setUp(): void
    {
        parent::setUp();
        File::deleteDirectory(database_path('cache-migrations/'));
        Carbon::setTestNow(self::TEST_DATETIME);
    }

    protected function getPackageProviders($app): array
    {
        return [
            CacheMigrationServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        File::deleteDirectory(database_path('cache-migrations/'));
    }
}
