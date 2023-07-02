<?php

namespace HaydarSahin\CacheMigration\Tests\Feature;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Mockery\Mock;

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

    /**
     * Check create migration status
     *
     * @return void
     */
    public function test_get_file_paths()
    {
        $this->createSampleMigrations();

        $this->assertSame(
            $this->command->getFilePaths(),
            [
                database_path('cache-migrations/2023_06_01_090000_sample_migration_one.php'),
                database_path('cache-migrations/2023_06_01_090000_sample_migration_two.php')
            ]
        );
    }

    /**
     * Check name of migrations which will be migrated
     *
     * @return void
     */
    public function test_get_migration_files()
    {
        $this->createSampleMigrations();
        $filesArray = $this->command->getMigrationFiles($this->command->getFilePaths());

        $this->assertSame(
            $filesArray,
            [
                '2023_06_01_090000_sample_migration_one' =>
                    database_path('cache-migrations/2023_06_01_090000_sample_migration_one.php'),
                '2023_06_01_090000_sample_migration_two' =>
                    database_path('cache-migrations/2023_06_01_090000_sample_migration_two.php')
            ]
        );
    }
}
