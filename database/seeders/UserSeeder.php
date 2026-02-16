<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'id_number' => 'ADMIN123',
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'first_name' => 'Mentor',
            'last_name' => 'User',
            'email' => 'mentor@example.com',
            'id_number' => 'MENTOR123',
            'role' => User::ROLE_MENTOR,
        ]);

        User::create([
            'first_name' => 'Participant',
            'last_name' => 'User',
            'email' => 'participant@example.com',
            'id_number' => 'PARTICIPANT123',
            'role' => User::ROLE_PARTICIPANT,
        ]);
    }
}
