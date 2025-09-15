<?php

namespace Tests\Feature;

use App\Models\Surgery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SurgeryConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $user->assignRole($role);

        return $user;
    }

    public function test_only_admin_or_nurse_can_confirm_surgery(): void
    {
        // patient cannot confirm
        $surgery = Surgery::factory()->create();
        $patient = User::factory()->create();
        Sanctum::actingAs($patient);
        $this->postJson("/api/surgeries/{$surgery->id}/confirm")
            ->assertForbidden();

        // doctor cannot confirm
        $surgery = Surgery::factory()->create();
        $doctor = $this->makeUser(User::ROLE_DOCTOR);
        Sanctum::actingAs($doctor);
        $this->postJson("/api/surgeries/{$surgery->id}/confirm")
            ->assertForbidden();

        // nurse can confirm and is recorded
        $surgery = Surgery::factory()->create();
        $nurse = $this->makeUser('nurse');
        Sanctum::actingAs($nurse);
        $this->postJson("/api/surgeries/{$surgery->id}/confirm")
            ->assertOk();
        $this->assertDatabaseHas('surgeries', [
            'id' => $surgery->id,
            'status' => Surgery::STATUS_CONFIRMED,
            'confirmed_by' => $nurse->id,
        ]);

        // admin can confirm and is recorded
        $surgery = Surgery::factory()->create();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Sanctum::actingAs($admin);
        $this->postJson("/api/surgeries/{$surgery->id}/confirm")
            ->assertOk();
        $this->assertDatabaseHas('surgeries', [
            'id' => $surgery->id,
            'status' => Surgery::STATUS_CONFIRMED,
            'confirmed_by' => $admin->id,
        ]);
    }

    public function test_only_admin_or_nurse_can_cancel_surgery(): void
    {
        // patient cannot cancel
        $surgery = Surgery::factory()->create();
        $patient = User::factory()->create();
        Sanctum::actingAs($patient);
        $this->postJson("/api/surgeries/{$surgery->id}/cancel")
            ->assertForbidden();

        // doctor cannot cancel
        $surgery = Surgery::factory()->create();
        $doctor = $this->makeUser(User::ROLE_DOCTOR);
        Sanctum::actingAs($doctor);
        $this->postJson("/api/surgeries/{$surgery->id}/cancel")
            ->assertForbidden();

        // nurse can cancel and is recorded
        $surgery = Surgery::factory()->create();
        $nurse = $this->makeUser('nurse');
        Sanctum::actingAs($nurse);
        $this->postJson("/api/surgeries/{$surgery->id}/cancel")
            ->assertOk();
        $this->assertDatabaseHas('surgeries', [
            'id' => $surgery->id,
            'status' => Surgery::STATUS_CANCELLED,
            'canceled_by' => $nurse->id,
        ]);

        // admin can cancel and is recorded
        $surgery = Surgery::factory()->create();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        Sanctum::actingAs($admin);
        $this->postJson("/api/surgeries/{$surgery->id}/cancel")
            ->assertOk();
        $this->assertDatabaseHas('surgeries', [
            'id' => $surgery->id,
            'status' => Surgery::STATUS_CANCELLED,
            'canceled_by' => $admin->id,
        ]);
    }
}
