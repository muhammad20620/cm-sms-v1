<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\GlobalSettings;

class InstallEkattor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install \
        {--db-host=127.0.0.1} \
        {--db-port=3306} \
        {--db-name=} \
        {--db-user=} \
        {--db-pass=} \
        {--purchase-code=success} \
        {--admin-name=Super Admin} \
        {--admin-email=admin@example.com} \
        {--admin-pass=secret} \
        {--system-name=Ekattor 8} \
        {--timezone=UTC} \
        {--skip-sql : Skip importing public/assets/install.sql} \
        {--force : Run non-interactively}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install locally using an existing MySQL database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $host = (string) $this->option('db-host');
        $port = (string) $this->option('db-port');
        $name = (string) $this->option('db-name');
        $user = (string) $this->option('db-user');
        $pass = (string) $this->option('db-pass');

        if (!$name || !$user) {
            $this->error('Provide --db-name and --db-user.');
            return Command::FAILURE;
        }

        $conn = 'install_'.Str::random(6);
        Config::set("database.connections.$conn", [
            'driver' => 'mysql',
            'host' => $host ?: '127.0.0.1',
            'port' => $port ?: '3306',
            'database' => $name,
            'username' => $user,
            'password' => $pass,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ]);

        try {
            DB::connection($conn)->getPdo();
        } catch (\Throwable $e) {
            $this->error('DB connection failed.');
            $this->line('• Host: '.$host.'  Port: '.$port.'  DB: '.$name.'  User: '.$user);
            $this->line('• Error: '.$e->getMessage());
            return Command::FAILURE;
        }

        // Persist env
        $this->writeEnv([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_DATABASE' => $name,
            'DB_USERNAME' => $user,
            'DB_PASSWORD' => $pass,
        ]);

        try {
            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            $this->error('Migration failed: '.$e->getMessage());
            return Command::FAILURE;
        }

        if (!$this->option('skip-sql')) {
            $sqlPath = base_path('public/assets/install.sql');
            if (File::exists($sqlPath)) {
                // If DB already has tables, warn and skip to avoid conflicts
                if ($this->databaseHasTables($conn, $name)) {
                    $this->warn('Database already has tables. Skipping install.sql import. Use --skip-sql to silence, or import into an empty DB.');
                } else {
                    $this->info('Importing install.sql ...');
                    $ok = $this->importSqlFileSafely($conn, $sqlPath);
                    if (!$ok) {
                        $this->error('install.sql import encountered errors. See messages above.');
                        return Command::FAILURE;
                    }
                }
            } else {
                $this->warn('install.sql not found at public/assets/install.sql; skipping.');
            }
        }

        // Configure database.php placeholders if present (mirrors web installer)
        $this->configureDatabasePhp($host, $user, $pass, $name);

        // Finalize setup: create session row, set globals, create admin, adjust routes
        $this->finalizeSetup();
        $this->createAdminIfMissing();
        $this->configureRoutesAfterInstall();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');

        $this->info('Installation completed.');
        return Command::SUCCESS;
    }

    private function writeEnv(array $pairs): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) {
            File::put($envPath, "APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\n");
        }
        $env = File::get($envPath);
        foreach ($pairs as $k => $v) {
            $pattern = "/^$k=.*/m";
            $line = $k.'='.(str_contains((string)$v, ' ') ? '"'.$v.'"' : $v);
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $env .= "\n$line";
            }
        }
        File::put($envPath, $env);
    }

    private function configureDatabasePhp(string $host, string $user, string $pass, string $db): void
    {
        $path = base_path('config/database.php');
        if (!File::exists($path)) return;
        $data = File::get($path);
        // replace common placeholders if present
        $data = str_replace(['db_host','db_user','db_pass','db_name'], [$host,$user,$pass,$db], $data);
        File::put($path, $data);
    }

    private function finalizeSetup(): void
    {
        // create an academic session if missing and set globals
        try {
            $sessionId = DB::table('sessions')->insertGetId([
                'session_title' => date('Y').'-'.(date('Y')+1),
                'status' => 1,
                'school_id' => null,
            ]);
            GlobalSettings::updateOrCreate(['key' => 'running_session'], ['value' => (string) $sessionId]);
        } catch (\Throwable $e) {
            // continue even if table differs
            $this->warn('Skipping academic session setup: '.$e->getMessage());
        }

        GlobalSettings::updateOrCreate(['key' => 'system_name'], ['value' => (string) $this->option('system-name')]);
        GlobalSettings::updateOrCreate(['key' => 'timezone'], ['value' => (string) $this->option('timezone')]);
        GlobalSettings::updateOrCreate(['key' => 'purchase_code'], ['value' => (string) ($this->option('purchase-code') ?: 'success')]);
    }

    private function createAdminIfMissing(): void
    {
        $email = (string) $this->option('admin-email');
        if (User::where('email', $email)->exists()) return;
        try {
            User::create([
                'name' => (string) $this->option('admin-name'),
                'email' => $email,
                'password' => bcrypt((string) $this->option('admin-pass')),
                'role_id' => 1,
                'user_information' => json_encode(['photo' => 'user.png']),
                'status' => 1,
            ]);
        } catch (\Throwable $e) {
            // Fallback insert without optional columns when schema differs
            try {
                DB::table('users')->insert([
                    'name' => (string) $this->option('admin-name'),
                    'email' => $email,
                    'password' => bcrypt((string) $this->option('admin-pass')),
                    'role_id' => 1,
                    'user_information' => json_encode(['photo' => 'user.png']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->warn('Admin created with minimal columns (schema differs: '.$e->getMessage().')');
            } catch (\Throwable $e2) {
                $this->error('Failed to create admin: '.$e2->getMessage());
            }
        }
    }

    private function configureRoutesAfterInstall(): void
    {
        $path = base_path('routes/web.php');
        if (!File::exists($path)) return;
        $data = File::get($path);
        $search = "Route::get('/', 'index')";
        $replace = "Route::get('/install_ended', 'index')";
        if (str_contains($data, $search)) {
            $data = str_replace($search, $replace, $data);
            File::put($path, $data);
        }
    }

    private function databaseHasTables(string $conn, string $database): bool
    {
        try {
            $row = DB::connection($conn)->selectOne(
                'SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = ?',
                [$database]
            );
            return ((int) ($row->c ?? 0)) > 0;
        } catch (\Throwable $e) {
            $this->warn('Could not inspect existing tables: '.$e->getMessage());
            return false;
        }
    }

    private function importSqlFileSafely(string $conn, string $path): bool
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error('Unable to open install.sql');
            return false;
        }
        $buffer = '';
        while (($line = fgets($handle)) !== false) {
            $trim = ltrim($line);
            // Skip comments and directives from dumps
            if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '/*') || str_starts_with($trim, '/*!') ||
                str_starts_with($trim, 'SET ') || str_starts_with($trim, 'START TRANSACTION') || str_starts_with($trim, 'COMMIT') ||
                str_starts_with($trim, 'LOCK TABLES') || str_starts_with($trim, 'UNLOCK TABLES') || str_starts_with($trim, 'DELIMITER')) {
                continue;
            }
            $buffer .= $line;
            if (preg_match('/;\s*$/', trim($line))) {
                $statement = trim($buffer);
                $buffer = '';
                try {
                    DB::connection($conn)->statement($statement);
                } catch (\Throwable $e) {
                    $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 220);
                    $this->error('Failed SQL: '.$preview.' ...');
                    $this->error('Reason: '.$e->getMessage());
                    fclose($handle);
                    return false;
                }
            }
        }
        fclose($handle);
        return true;
    }
}
