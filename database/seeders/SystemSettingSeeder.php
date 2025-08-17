<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::insert([
            [
                'id'             => 1,
                'system_name'    => 'Laravel Stater Kit',
                'email'          => 'support@gmail.com',
                'logo'           => 'backend/images/Logo.png',
                'favicon'        => 'backend/images/Logo.png',
                'created_at'     => Carbon::now(),
            ],
        ]);
    }
}
