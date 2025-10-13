<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostInstallSeeder extends Seeder
{
    /**
     * Run the database seeds that should occur after raw install.sql is executed.
     * These seeds are idempotent and will not duplicate existing data.
     *
     * @return void
     */
    public function run()
    {
        // Seed additional global settings via existing seeder (idempotent)
        $this->call([
            GlobalSettingsSeeder::class,
        ]);

        // Ensure core roles exist (aligns with expected role IDs used elsewhere)
        $roles = [
            ['role_id' => 1, 'name' => 'Super Admin'],
            ['role_id' => 2, 'name' => 'Admin'],
            ['role_id' => 3, 'name' => 'Student'],
        ];

        foreach ($roles as $role) {
            $exists = DB::table('roles')->where('role_id', $role['role_id'])->exists();
            if (!$exists) {
                try {
                    DB::table('roles')->insert([
                        'role_id' => $role['role_id'],
                        'name' => $role['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Skipping role insert', ['role_id' => $role['role_id'], 'error' => $e->getMessage()]);
                }
            }
        }
    }
}




