<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $info = array(
            'gender' => "Male",
            'blood_group' => "a+",
            'birthday' => time(),
            'phone' => '1234567890',
            'address' => 'address',
            'photo' => "user.png"
        );


        $user = [
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@example.com',
                'role_id' => '1',
                'password' => bcrypt('password'),
                'user_information' => json_encode($info),
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
