<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurgeryConflictTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the admin role exists for the sanctum guard
        Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
    }

    public function test_conflict_is_marked_when_max_rooms_exceeded(): void
    {
        Setting::setValue('max_rooms', 1);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $start = now()->addDay()->setTime(10, 0);

        $payload = [
            'patient_name' => 'Patient One',
            'starts_at' => $start->toDateTimeString(),
            'duration_min' => 60,
            'surgery_type' => 'type',
            'room' => 1,
        ];

        $first = $this->postJson('/api/surgeries', $payload);
        $first->assertStatus(201);
        $this->assertFalse($first->json('is_conflict'));

        $second = $this->postJson('/api/surgeries', [
            'patient_name' => 'Patient Two',
            'starts_at' => $start->toDateTimeString(),
            'duration_min' => 60,
            'surgery_type' => 'type',
            'room' => 1,
        ]);
        $second->assertStatus(201);
        $this->assertTrue($second->json('is_conflict'));

        $list = $this->getJson('/api/surgeries');
        $list->assertStatus(200);
        $list->assertJsonPath('data.0.is_conflict', true);
        $list->assertJsonPath('data.1.is_conflict', true);
    }
}

