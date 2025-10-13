<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaults = [
            ['key' => 'language', 'value' => 'english'],
            ['key' => 'version', 'value' => '1.0'],
            ['key' => 'system_currency', 'value' => 'USD'],
        ];

        foreach ($defaults as $item) {
            $exists = DB::table('global_settings')->where('key', $item['key'])->exists();
            if (!$exists) {
                DB::table('global_settings')->insert($item);
            }
        }

        // Ensure english language exists minimally
        if (!DB::table('language')->where('name', 'english')->exists()) {
            DB::table('language')->insert([
                'name' => 'english',
                'phrase' => 'Email',
                'translated' => 'Email',
            ]);
        }
    }
}


