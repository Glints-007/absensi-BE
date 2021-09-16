<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'uid' => Str::uuid(),
            'name' => 'Admin',
            'email' => 'glints007.noreply@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('glints007'),
            'office_id' => '1',
            'status' => 'verified',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('users')->insert([
            'uid' => Str::uuid(),
            'name' => 'Andra',
            'email' => 'andra.gws@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'office_id' => '1',
            'status' => 'verified',
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
