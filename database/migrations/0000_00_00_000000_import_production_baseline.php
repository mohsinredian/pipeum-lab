<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Imports the production baseline (schema + data) from database/dump/baseline.sql.
 *
 * Why this exists:
 * The historical migrations were authored out of order and some tables/columns
 * were created manually on production via the MySQL UI, so they cannot be replayed
 * cleanly on a fresh database. The production SQL dump is the single source of truth
 * for both the schema and the master/user data, so a new environment is fully set up
 * with a single `php artisan migrate`.
 *
 * The dump already contains the `migrations` table and its rows, so after this runs
 * Laravel treats all historical migrations as already applied and will not replay them.
 */
return new class extends Migration
{
    public function up(): void
    {
        $path = database_path('dump/baseline.sql');

        if (!is_file($path)) {
            // No dump present (CI, fresh environments) - skip the import
            return;
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("Unable to read baseline dump at: {$path}");
        }

        // Laravel created its own `migrations` table before running this migration.
        // The dump also defines `migrations` (with the production rows we want), so
        // drop the empty one first, then let the dump recreate and repopulate it.
        Schema::dropIfExists('migrations');

        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');

        try {
            // Execute the whole dump. Statements are separated by ";\n"; running the
            // file as-is via unprepared() lets MySQL handle it as a script.
            foreach ($this->splitStatements($sql) as $statement) {
                DB::unprepared($statement);
            }
        } finally {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->markVendorMigrationsAsRun();
    }

    /**
     * Some packages (e.g. Laravel Sanctum) auto-register their own migrations from
     * vendor/. The production dump already contains the tables those migrations would
     * create, but the dump's `migrations` table may not list them under the exact
     * vendor filename. Without this, `migrate` would try to run them and fail with
     * "table already exists". Here we record any auto-registered migration whose name
     * is not yet in the `migrations` table, so Laravel treats it as already applied.
     */
    private function markVendorMigrationsAsRun(): void
    {
        // Highest batch currently recorded, so these land in a consistent batch.
        $batch = (int) (DB::table('migrations')->max('batch') ?? 1);

        $known = DB::table('migrations')->pluck('migration')->all();
        $knownLookup = array_flip($known);

        // Discover every migration file the framework knows about (app + vendor paths).
        $paths = app('migrator')->paths();
        $paths[] = database_path('migrations');
        $paths = array_unique($paths);

        $names = [];
        foreach ($paths as $path) {
            foreach (glob(rtrim($path, '/').'/*.php') ?: [] as $file) {
                $names[] = basename($file, '.php');
            }
        }
        $names = array_unique($names);

        foreach ($names as $name) {
            if ($name === '0000_00_00_000000_import_production_baseline') {
                continue; // Laravel records this one itself.
            }
            if (!isset($knownLookup[$name])) {
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch' => $batch,
                ]);
            }
        }
    }

    public function down(): void
    {
        // The baseline is not reversible. Use a fresh database to re-import.
        throw new RuntimeException(
            'The production baseline import cannot be rolled back. Drop and recreate the database instead.'
        );
    }

    /**
     * Split a dump into individual statements, ignoring semicolons that appear
     * inside quoted strings or comments. Handles the plain phpMyAdmin dump format
     * used by this project (no triggers/procedures/DELIMITER blocks).
     *
     * @return iterable<string>
     */
    private function splitStatements(string $sql): iterable
    {
        $len = strlen($sql);
        $buffer = '';
        $inSingle = false;
        $inDouble = false;
        $inBacktick = false;

        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            $prev = $i > 0 ? $sql[$i - 1] : '';

            // Strip line comments (-- ... and # ...) only when not inside a string.
            if (!$inSingle && !$inDouble && !$inBacktick) {
                // -- comment (requires the -- to be at a statement/line boundary)
                if ($ch === '-' && $i + 1 < $len && $sql[$i + 1] === '-'
                    && ($buffer === '' || substr($buffer, -1) === "\n")) {
                    while ($i < $len && $sql[$i] !== "\n") {
                        $i++;
                    }
                    continue;
                }
                // # comment
                if ($ch === '#' && ($buffer === '' || substr($buffer, -1) === "\n")) {
                    while ($i < $len && $sql[$i] !== "\n") {
                        $i++;
                    }
                    continue;
                }
                // /* ... */ block comment (including /*!40101 ... */ conditional comments,
                // which are safe to skip for this import)
                if ($ch === '/' && $i + 1 < $len && $sql[$i + 1] === '*') {
                    $i += 2;
                    while ($i + 1 < $len && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                        $i++;
                    }
                    $i++; // land on '/'
                    continue;
                }
            }

            // Track quote state (respecting backslash escapes).
            if ($ch === "'" && !$inDouble && !$inBacktick && $prev !== '\\') {
                $inSingle = !$inSingle;
            } elseif ($ch === '"' && !$inSingle && !$inBacktick && $prev !== '\\') {
                $inDouble = !$inDouble;
            } elseif ($ch === '`' && !$inSingle && !$inDouble) {
                $inBacktick = !$inBacktick;
            }

            if ($ch === ';' && !$inSingle && !$inDouble && !$inBacktick) {
                $statement = trim($buffer);
                if ($statement !== '') {
                    yield $statement;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $ch;
        }

        $statement = trim($buffer);
        if ($statement !== '') {
            yield $statement;
        }
    }
};
