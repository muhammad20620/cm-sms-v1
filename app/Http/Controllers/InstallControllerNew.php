<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GlobalSettings;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InstallControllerNew extends Controller
{
    /**
     * Show the installer entry.
     */
    public function index(): RedirectResponse
    {
        $installed = (bool) config('app.installed', filter_var(env('APP_INSTALLED', false), FILTER_VALIDATE_BOOL));
        if ($installed) {
            return redirect()->route('login');
        }
        return redirect()->route('step0');
    }

    public function step0(): Renderable
    {
        return view('install.step0');
    }

    public function step1(): Renderable
    {
        return view('install.step1');
    }

    public function step2(string $param1 = ''): Renderable
    {
        $error = $param1 === 'error' ? 'Purchase Code Verification Failed' : '';
        return view('install.step2', ['error' => $error]);
    }

    public function validatePurchaseCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_code' => ['required', 'string', 'max:255'],
        ]);

        $purchaseCode = $validated['purchase_code'];

        $shouldVerify = (bool) filter_var(env('VERIFY_PURCHASE', false), FILTER_VALIDATE_BOOL);
        $token = env('ENVATO_TOKEN');

        $validationResponse = true;

        if ($shouldVerify && !empty($token)) {
            try {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(10)
                    ->get('https://api.envato.com/v3/market/author/sale', [
                        'code' => $purchaseCode,
                    ]);

                $validationResponse = $response->successful();
            } catch (\Throwable $e) {
                Log::warning('Purchase verification failed', ['error' => $e->getMessage()]);
                $validationResponse = false;
            }
        }

        if ($validationResponse === true) {
            session(['purchase_code' => $purchaseCode]);
            session(['purchase_code_verified' => 1]);
            return redirect()->route('step3');
        }

        session(['purchase_code_verified' => 0]);
        return redirect()->route('step2', ['error' => 'error']);
    }

    public function step3(Request $request): Renderable|RedirectResponse
    {
        $dbConnection = '';
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'hostname' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'password' => ['nullable', 'string'],
                'dbname'   => ['required', 'string', 'max:255'],
            ]);

            $hostname = $validated['hostname'];
            $username = $validated['username'];
            $password = $validated['password'] ?? '';
            $dbname   = $validated['dbname'];

            $dbConnection = $this->checkDatabaseConnection($hostname, $username, $password, $dbname);
            if ($dbConnection === 'success') {
                session(['hostname' => $hostname]);
                session(['username' => $username]);
                session(['password' => $password]);
                session(['dbname' => $dbname]);
                return redirect()->route('step4');
            }
        }

        return view('install.step3', ['db_connection' => $dbConnection]);
    }

    public function step4(): Renderable
    {
        return view('install.step4');
    }

    public function confirmImport(string $param1 = ''): Renderable
    {
        if ($param1 === 'confirm_import') {
            $this->writeEnv([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST'       => (string) session('hostname', '127.0.0.1'),
                'DB_PORT'       => env('DB_PORT', '3306'),
                'DB_DATABASE'   => (string) session('dbname', ''),
                'DB_USERNAME'   => (string) session('username', ''),
                'DB_PASSWORD'   => (string) session('password', ''),
            ]);

            return view('install.install');
        }

        return view('install.step4');
    }

    public function confirmInstall(): RedirectResponse
    {
        $sqlPath = base_path('public/assets/install.sql');
        $this->runInstallSql($sqlPath);
        // Run post-install seeds to populate remaining data
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\PostInstallSeeder',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            Log::warning('PostInstallSeeder failed', ['error' => $e->getMessage()]);
        }
        return redirect()->route('finalizing_setup');
    }

    public function finalizingSetup(Request $request): Renderable|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'current_session'     => ['required', 'string', 'max:255'],
                'system_name'         => ['required', 'string', 'max:255'],
                'timezone'            => ['required', 'string', 'max:255'],
                'superadmin_name'     => ['required', 'string', 'max:255'],
                'superadmin_email'    => ['required', 'email', 'max:255'],
                'superadmin_password' => ['required', 'string', 'min:8'],
                'superadmin_phone'    => ['nullable', 'string', 'max:50'],
                'superadmin_address'  => ['nullable', 'string', 'max:255'],
            ]);

            DB::beginTransaction();
            try {
                $sessionId = DB::table('sessions')->insertGetId([
                    'session_title' => $validated['current_session'],
                    'status' => 1,
                ]);

                $systemData = [
                    'running_session' => (string) $sessionId,
                    'system_name' => $validated['system_name'],
                    'timezone' => $validated['timezone'],
                ];

                if (session('purchase_code')) {
                    $systemData['purchase_code'] = (string) session('purchase_code');
                }

                foreach ($systemData as $key => $value) {
                    $existing = GlobalSettings::where('key', $key)->first();
                    if ($existing) {
                        $existing->update(['value' => $value]);
                    } else {
                        GlobalSettings::create(['key' => $key, 'value' => $value]);
                    }
                }

                $userInfo = [
                    'gender' => 'Male',
                    'blood_group' => 'a+',
                    'birthday' => time(),
                    'phone' => $validated['superadmin_phone'] ?? '',
                    'address' => $validated['superadmin_address'] ?? '',
                    'photo' => 'user.png',
                ];

                $existingAdmin = User::where('email', $validated['superadmin_email'])->first();
                if (!$existingAdmin) {
                    User::create([
                        'name' => $validated['superadmin_name'],
                        'email' => $validated['superadmin_email'],
                        'password' => Hash::make($validated['superadmin_password']),
                        'role_id' => 1,
                        'user_information' => json_encode($userInfo),
                        'status' => 1,
                    ]);
                }

                DB::commit();

                $this->writeEnv([
                    'APP_INSTALLED' => 'true',
                ]);

                return redirect()->route('success');
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Finalizing setup failed', ['error' => $e->getMessage()]);
                return back()->withErrors(['install' => 'Installation failed. Check logs for details.']);
            }
        }

        return view('install.finalizing_setup');
    }

    public function success(string $param1 = ''): Renderable
    {
        if ($param1 === 'login') {
            return view('auth.login');
        }

        $superadmin = User::find(1);
        $superadminEmail = $superadmin?->email ?? '';
        return view('install.success', ['admin_email' => $superadminEmail]);
    }

    private function checkDatabaseConnection(string $hostname, string $username, string $password, string $dbname): string
    {
        $dbConnName = uniqid('db', true) . '_conn';
        Config::set("database.connections." . $dbConnName, [
            'driver'    => 'mysql',
            'host'      => $hostname ?: '127.0.0.1',
            'port'      => env('DB_PORT', '3306'),
            'database'  => $dbname,
            'username'  => $username,
            'password'  => $password,
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'collation' => 'utf8mb4_unicode_ci',
            'strict'    => true,
        ]);

        try {
            DB::connection($dbConnName)->getPdo();
            return 'success';
        } catch (\Throwable $e) {
            Log::warning('DB connection failed', ['error' => $e->getMessage()]);
            return 'Could not connect to the database. Please check your configuration.';
        }
    }

    private function writeEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            Log::warning('.env not writable or missing');
            return;
        }

        $env = file_get_contents($envPath);
        foreach ($data as $key => $value) {
            $escaped = preg_quote($key, '/');
            $pattern = "/^{$escaped}=.*$/m";
            $line = $key . '=' . $this->quoteEnvValue((string) $value);
            if (preg_match($pattern, $env)) {
                $env = (string) preg_replace($pattern, $line, $env);
            } else {
                $env .= PHP_EOL . $line;
            }
        }
        file_put_contents($envPath, $env);
    }

    private function quoteEnvValue(string $value): string
    {
        if ($value === '') {
            return "\"\"";
        }
        if (preg_match('/\s|#|=|"/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }
        return $value;
    }

    private function runInstallSql(string $sqlFilePath): void
    {
        if (!is_file($sqlFilePath)) {
            Log::warning('install.sql not found', ['path' => $sqlFilePath]);
            return;
        }

        $templine = '';
        $lines = file($sqlFilePath);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                    continue;
                }
                $templine .= $line;
                if (substr($trimmed, -1) === ';') {
                    try {
                        DB::unprepared($templine);
                    } catch (\Throwable $e) {
                        Log::error('SQL execution failed', [
                            'statement' => $templine,
                            'error' => $e->getMessage(),
                        ]);
                        // Continue to attempt remaining statements
                    }
                    $templine = '';
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}


