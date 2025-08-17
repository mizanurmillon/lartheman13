<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'is_premium' => false,
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'is_premium' => false,
                'name' => 'Leader',
                'email' => 'leader@leader.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'leader',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'is_premium' => false,
                'name' => 'Member',
                'email' => 'member@member.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'member',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'is_premium' => true,
                'name' => 'Md Mizanur Rahman',
                'email' => 'mr7517218@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'viewer',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
        ]);
    }
}
