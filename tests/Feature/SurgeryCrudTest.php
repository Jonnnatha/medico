<?php

namespace Tests\Feature;

use App\Models\Surgery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SurgeryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (['admin', 'doctor', 'nurse', 'patient'] as $role) {
            Role::create(['name' => $role]);
        }
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $user->assignRole($role);

        return $user;
    }

    public function test_index_returns_ok_for_all_roles(): void
    {
        foreach (['admin', 'doctor', 'nurse', 'patient'] as $role) {
            $user = $this->createUserWithRole($role);
            $response = $this->actingAs($user, 'sanctum')->getJson('/api/surgeries');
            $response->assertOk();
        }
    }

    public function test_store_permissions(): void
    {
        $doctor = $this->createUserWithRole('doctor');

        $data = [
            'patient_name' => 'John Doe',
            'starts_at' => now()->addDay()->toISOString(),
            'duration_min' => 60,
            'surgery_type' => 'test',
            'room' => 1,
            'doctor_id' => $doctor->id,
        ];

        $admin = $this->createUserWithRole('admin');
        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/surgeries', $data)
            ->assertCreated();

        $this->actingAs($doctor, 'sanctum')
            ->postJson('/api/surgeries', collect($data)->except('doctor_id')->toArray())
            ->assertCreated();

        foreach (['nurse', 'patient'] as $role) {
            $user = $this->createUserWithRole($role);
            $this->actingAs($user, 'sanctum')
                ->postJson('/api/surgeries', $data)
                ->assertForbidden();
        }
    }

    public function test_update_permissions(): void
    {
        $doctor = $this->createUserWithRole('doctor');
        $surgery = Surgery::factory()->create(['doctor_id' => $doctor->id]);

        $admin = $this->createUserWithRole('admin');
        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/surgeries/{$surgery->id}", ['room' => 2])
            ->assertOk();

        $this->actingAs($doctor, 'sanctum')
            ->putJson("/api/surgeries/{$surgery->id}", ['room' => 3])
            ->assertOk();

        foreach (['nurse', 'patient'] as $role) {
            $user = $this->createUserWithRole($role);
            $this->actingAs($user, 'sanctum')
                ->putJson("/api/surgeries/{$surgery->id}", ['room' => 4])
                ->assertForbidden();
        }
    }

    public function test_destroy_permissions(): void
    {
        $doctor = $this->createUserWithRole('doctor');
        $surgeryForAdmin = Surgery::factory()->create(['doctor_id' => $doctor->id]);

        $admin = $this->createUserWithRole('admin');
        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/surgeries/{$surgeryForAdmin->id}")
            ->assertNoContent();

        $surgeryForDoctor = Surgery::factory()->create(['doctor_id' => $doctor->id]);
        $this->actingAs($doctor, 'sanctum')
            ->deleteJson("/api/surgeries/{$surgeryForDoctor->id}")
            ->assertNoContent();

        foreach (['nurse', 'patient'] as $role) {
            $user = $this->createUserWithRole($role);
            $surgery = Surgery::factory()->create(['doctor_id' => $doctor->id]);
            $this->actingAs($user, 'sanctum')
                ->deleteJson("/api/surgeries/{$surgery->id}")
                ->assertForbidden();
        }
    }
}
