<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Sanctuary / Worship Center',
            'Lobby',
            'Fellowship Hall',
            'Multipurpose Room',
            'Stage / Platform',
            'Children\'s Area',
            'Main Entrance',
            'Side Entrance / Rear Exit',
            'Parking Lot',
            'Nursery',
            'Children’s Ministry Room',
            'Youth Room / Teen Center',
            'Sunday School Classrooms',
            'Church Office',
            'Pastor’s Office',
            'Bathrooms',
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['name' => $loc]);
        }
    }
}
