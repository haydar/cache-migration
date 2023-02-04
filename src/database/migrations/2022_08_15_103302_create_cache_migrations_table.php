<?php

namespace HaydarSahin\CacheMigration\Commands;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCacheMigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_migrations');
    }
}
