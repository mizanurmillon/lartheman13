<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('denominations')->insert([
            ['name' => 'Christian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Catholic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Muslim', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orthodox', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Anglican', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Baptist', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
