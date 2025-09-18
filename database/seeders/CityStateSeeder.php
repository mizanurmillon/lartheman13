<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CityStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'New York',
            'Los Angeles',
            'Chicago',
        ];

        foreach ($cities as $city) {
            $cityId = DB::table('cities')->insertGetId([
                'name' => $city,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            
            $states = [
                ['name' => $city . ' - Downtown'],
                ['name' => $city . ' - Uptown'],
                ['name' => $city . ' - Suburb'],
            ];

            foreach ($states as $state) {
                DB::table('states')->insert([
                    'city_id' => $cityId,
                    'name' => $state['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    
    }
}
