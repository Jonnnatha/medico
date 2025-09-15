<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_max_rooms_setting(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($admin)
            ->from('/settings')
            ->post('/settings', [
                'max_rooms' => 12,
            ]);

        $response->assertRedirect('/settings');

        $this->assertSame('12', Setting::getValue('max_rooms'));
    }
}
