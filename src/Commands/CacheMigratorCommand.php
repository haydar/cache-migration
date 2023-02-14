<?php

namespace HaydarSahin\CacheMigration\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CacheMigratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:migrate --v|verbose {--batch=} {--l|last}';

    /**
     * @var Filesystem
     */
    public $files;

    /**
     * @var int
     */
    private $batch;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the pending cache migrations';

    /**
     * Execute the migrate command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $filesArray = $this->getMigrationFiles($this->getFilePaths());

        $ranMigrations = $this->getRanMigrations();
        $this->batch = $ranMigrations === [] ? 0 : ++last($ranMigrations)->batch;

        $pendingMigrations = $this->pendingMigrations($filesArray, array_column($ranMigrations, 'migration'));

        $this->requireFiles($pendingMigrations);

        $this->runPending($pendingMigrations);
    }

    /**
     * Get the name of the migration.
     *
     * @param string $path
     * @return string
     */
    public function getMigrationName(string $path): string
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * Get all the migration files in a given path.
     *
     * @param string|array $paths
     * @return array
     */
    public function getMigrationFiles($paths): array
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path . '/*_*.php');
        })->filter()->sortBy(function ($file) {
            return $this->getMigrationName($file);
        })->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        })->all();
    }

    /**
     * Get file path of cache migrations
     *
     * @return array
     */
    public function getFilePaths(): array
    {
        $fileInfos = File::isDirectory(database_path('cache-migrations'))
            ? File::allFiles(database_path('cache-migrations'))
            : [];

        $this->files = new Filesystem();
        $paths = [];

        foreach ($fileInfos as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /**
     * Get ran migrations
     *
     * @return array
     */
    private function getRanMigrations(): array
    {
        return DB::table('cache_migrations')
            ->select('migration', 'batch')
            ->get()
            ->toArray();
    }

    /**
     * Get the migration files that have not yet run.
     *
     * @param array $files
     * @param array $ran
     * @return array
     */
    private function pendingMigrations(array $files, array $ran): array
    {
        $batchOption = $this->option('batch');

        // Get migrations of the old batch
        if (!is_null($batchOption)) {
            return $this->getMigrationsByBatch($batchOption);
        }

        if ($this->option('last')) {
            return $this->getMigrationsByBatch($this->batch - 1);
        }

        // Compare cache migration files with db records
        return Collection::make($files)
            ->reject(function ($file) use ($ran) {
                return in_array($this->getMigrationName($file), $ran);
            })->values()->all();
    }

    /**
     * Get migrations by batch number
     *
     * @param $batch
     * @return array
     */
    private function getMigrationsByBatch($batch)
    {
        return Collection::make($this->getRanMigrations())->where('batch', $batch)
            ->pluck('migration')
            ->transform(function ($item) {
                return database_path('cache-migrations') . DIRECTORY_SEPARATOR . $item . '.php';
            })->toArray();
    }

    /**
     * Require in all the migration files in a given path.
     *
     * @param array $files
     * @return void
     */
    public function requireFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->files->requireOnce($file);
        }
    }

    /**
     * Run all pending migrations
     *
     * @param array $migrations
     * @return void
     */
    private function runPending(array $migrations): void
    {
        if (empty($migrations)) {
            $this->info('Nothing cache migration to migrate.');

            return;
        }

        foreach ($migrations as $migration) {
            $this->runUp($migration);
        }
    }

    /**
     * Run migration process
     *
     * @param string $file
     * @return void
     */
    protected function runUp(string $file): void
    {
        // Generate an instance of the migration file
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        $this->info("<comment>Migrating:</comment> {$name}");


        if (!property_exists($migration, 'patterns')) {
            $this->warn("There is no patterns property");
            return;
        }

        if (in_array('', $migration->patterns) || count($migration->patterns) < 1) {
            $this->warn("Patterns property or its a item are empty");
            return;
        }

        foreach ($migration->patterns as $key => $pattern) {
            $this->info("<comment>Pattern :</comment> $pattern");

            if (!$this->isValidPattern($pattern)) {
                $this->error("Invalid pattern. Index : $key");
                continue;
            }

            $patternKey = config('cache.prefix').":$pattern";

            $keys = Redis::KEYS($patternKey);

            if (count($keys) == 0) {
                $this->info("There is no active cache: {$name}");
                $this->saveRanMigration($name);
                continue;
            }

            $confirmed = $this->option('verbose') || $this->confirm(
                    "Are you sure for deleting all items of the $patternKey?"
                );

            if ($confirmed) {
                $deletedRecords = Redis::command('DEL', $keys);

                $this->info("$deletedRecords record(s) has been deleted");
                $this->saveRanMigration($name);
            }
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param string $file
     * @return object
     */
    public function resolve(string $file): object
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        return new $class();
    }

    /**
     * Check the $pattern for preventing unhandled redis error
     *
     * @param $pattern
     * @return bool
     */
    private function isValidPattern($pattern): bool
    {
        return is_string($pattern) && $pattern !== '*' && strlen($pattern) > 2;
    }

    /**
     * Save migration record to db
     *
     * @param string $migrationName
     * @return void
     */
    private function saveRanMigration(string $migrationName)
    {
        DB::table('cache_migrations')->insert(
            [
                'migration' => $migrationName,
                'batch' => $this->batch
            ]
        );
    }


}
