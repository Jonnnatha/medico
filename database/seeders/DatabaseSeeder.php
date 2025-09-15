<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => User::ROLE_NURSE]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
            'role' => User::ROLE_DOCTOR,
        ]);

        User::factory()->create([
            'name' => 'Patient User',
            'email' => 'patient@example.com',
            'role' => User::ROLE_PATIENT,
        ]);

        User::factory()->create([
            'name' => 'Nurse User',
            'email' => 'nurse@example.com',
            'role' => User::ROLE_NURSE,
        ])->assignRole(User::ROLE_NURSE);
    }
}
