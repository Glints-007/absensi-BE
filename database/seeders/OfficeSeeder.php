<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('offices')->insert([
            'name' => 'Glints',
            'lat' => -6.271542,
            'long' => 106.795151,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
