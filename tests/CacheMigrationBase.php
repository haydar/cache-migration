<?php

namespace HaydarSahin\CacheMigration\Tests;
use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;

abstract class CacheMigrationBase extends TestCase
{
    protected const TEST_DATETIME = '2023-06-01 09:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(self::TEST_DATETIME);
    }

    protected function getPackageProviders($app): array
    {
        return [
            \HaydarSahin\CacheMigration\Tests\CacheMigrationServiceProvider::class,
        ];
    }
}
