<?php

namespace HaydarSahin\CacheMigration\Tests\Feature;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CacheMigratorCommandTest extends CacheMigrationBase
{
    use RefreshDatabase;

    /**
     * Try to migrate cache without any migrations
     *
     * @return void
     */
    public function test_migrate_without_migrations()
    {
        $this->artisan('cache:migrate')
            ->expectsOutput('Nothing cache migration to migrate.')
            ->assertExitCode(0);
    }

    /**
     * Try to migrate without any cache pattern (So means default file)
     *
     * @return void
     */
    public function test_migrate_without_pattern()
    {
        $this->createSampleMigrations();
        $this->artisan('cache:migrate')
            ->expectsOutput('Migrating: 2023_06_01_090000_sample_migration_one')
            ->expectsOutput('Patterns property or its a item are empty')
            ->expectsOutput('Migrating: 2023_06_01_090000_sample_migration_two')
            ->expectsOutput('Patterns property or its a item are empty')
            ->assertExitCode(0);

    }
}
