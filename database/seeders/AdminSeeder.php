<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            "last_name" => "admin",
            "username" => "admin",
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'phone' => '1234567890',
            'user_role' => 'admin',
        ]);
    }
}
