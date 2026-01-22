<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'aklanul',
                'email' => 'aklanul@msa.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'raharjo',
                'email' => 'raharjo@msa.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'saifudin',
                'email' => 'saifudin@msa.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'rani',
                'email' => 'rani@msa.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'ria',
                'email' => 'ria@msa.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'yayuk',
                'email' => 'yayuk@msa.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'kadarusman',
                'email' => 'kadarusman@msa.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
