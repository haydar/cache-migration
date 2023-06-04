<?php

namespace HaydarSahin\CacheMigration\Tests\Feature;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;

class AddCacheMigrationCommandTest extends CacheMigrationBase
{

    /**
     * Check creating migration files
     *
     * @return void
     */
    public function test_file_stored_successfully()
    {
        $migrationPath = database_path('cache-migrations/2023_06_01_090000_sample_migration.php');

        $this->artisan('make:cache-migration sampleMigration')
            ->expectsOutput('Cache migration created successfully.')
            ->assertExitCode(0);

        $this->assertFileExists($migrationPath);
    }
}
