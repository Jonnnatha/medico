<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            User::ROLE_ADMIN,
            User::ROLE_DOCTOR,
            User::ROLE_NURSE,
            User::ROLE_PATIENT,
        ] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ])->assignRole(User::ROLE_ADMIN);

        User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
            'role' => User::ROLE_DOCTOR,
        ])->assignRole(User::ROLE_DOCTOR);

        User::factory()->create([
            'name' => 'Nurse User',
            'email' => 'nurse@example.com',
            'role' => User::ROLE_NURSE,
        ])->assignRole(User::ROLE_NURSE);

        User::factory()->create([
            'name' => 'Patient User',
            'email' => 'patient@example.com',
            'role' => User::ROLE_PATIENT,
        ])->assignRole(User::ROLE_PATIENT);
    }
}
