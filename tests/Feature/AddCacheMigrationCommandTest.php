<?php

namespace HaydarSahin\CacheMigration\Tests\Feature;

use HaydarSahin\CacheMigration\Tests\CacheMigrationBase;

class AddCacheMigrationCommandTest extends CacheMigrationBase
{

    /**
     * Check create migration status
     *
     * @return void
     */
    public function testisCacheMigrationCreatingSuccessfully()
    {
        $this->artisan('make:cache-migration clear')
            ->expectsOutput('Cache migration created successfully.')
            ->assertExitCode(0);
    }
}
