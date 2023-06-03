<?php

namespace HaydarSahin\CacheMigration\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class AddCacheMigrationCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:cache-migration {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new cache migration class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Cache migration';


    /**
     * Get stub path
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../Stubs/cache-migration.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name): string
    {
        $name = sprintf(
            '%s%s',
            $this->getDatePrefix(),
            Str::ucfirst(
                Str::replaceFirst($this->rootNamespace(), '', $name)
            )
        );

        $name = Str::snake($name);

        return database_path('cache-migrations') . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix(): string
    {
        return now()->format('Y_m_d_His');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name): string
    {
        $class = ucfirst(str_replace($this->getNamespace($name) . '\\', '', $name));

        return str_replace('DummyClass', $class, $stub);
    }

}
