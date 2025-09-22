<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\TrainingProgram;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TrainingProgramTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TrainingProgram::insert([
            [
                'title' => 'How to Gard a vip',
                'description' =>'How to Gard a vip client proper way tutorial',
                'thumbnail' => 'backend/images/Thumbnail (1).png',
                'video' => 'backend/images/3196218-uhd_3840_2160_25fps.mp4',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'How to Gard a vip 1',
                'description' =>'How to Gard a vip client proper way tutorial',
                'thumbnail' => 'backend/images/Thumbnail (2).png',
                'video' => 'backend/images/4065388-uhd_3840_2160_30fps.mp4',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'How to Gard a vip 2',
                'description' =>'How to Gard a vip client proper way tutorial',
                'thumbnail' => 'backend/images/Thumbnail (3).png',
                'video' => 'backend/images/4438080-hd_1920_1080_25fps.mp4',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'How to Gard a vip client',
                'description' =>'How to Gard a vip client proper way tutorial',
                'thumbnail' => 'backend/images/Thumbnail (4).png',
                'video' => 'backend/images/4109367-uhd_3840_2160_25fps.mp4',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ]);
    }
}
