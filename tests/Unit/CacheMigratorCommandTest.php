<?php

namespace HaydarSahin\CacheMigration\Tests\Unit;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;

class CacheMigratorCommandFeatureTest extends CacheMigrationBase
{
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
     * Check name of migrations which is will be migrated
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
