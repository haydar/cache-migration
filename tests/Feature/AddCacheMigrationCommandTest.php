<?php

namespace HaydarSahin\CacheMigration\Tests\Feature;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;
use Illuminate\Support\Facades\File;

class AddCacheMigrationCommandTest extends CacheMigrationBase
{

    /**
     * Check create migration status
     *
     * @return void
     */
    public function testIsCacheMigrationCreatingSuccessfully()
    {
        $migrationPath = database_path('cache-migrations/2023_06_01_090000_sample_migration.php');
        File::delete($migrationPath);

        $this->artisan('make:cache-migration sampleMigration')
            ->expectsOutput('Cache migration created successfully.')
            ->assertExitCode(0);

        $this->assertFileExists($migrationPath);
    }
}
