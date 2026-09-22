<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioSettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_admin_can_open_payment_and_email_settings(): void
    {
        $org = Organization::factory()->create();
        $admin = User::factory()->create();
        $org->users()->attach($admin->id, ['role' => 'studio_admin', 'accepted_at' => now()]);
        Tenant::set($org->id);

        $this->actingAs($admin)
            ->withSession(['current_organization_id' => $org->id])
            ->get('/app/settings/payments')
            ->assertOk()
            ->assertSee('Payment gateway');

        $this->actingAs($admin)
            ->withSession(['current_organization_id' => $org->id])
            ->get('/app/settings/email')
            ->assertOk()
            ->assertSee('Email / SMTP');
    }

    public function test_manager_cannot_open_payment_settings(): void
    {
        $org = Organization::factory()->create();
        $manager = User::factory()->create();
        $org->users()->attach($manager->id, ['role' => 'manager', 'accepted_at' => now()]);
        Tenant::set($org->id);

        $this->actingAs($manager)
            ->withSession(['current_organization_id' => $org->id])
            ->get('/app/settings/payments')
            ->assertForbidden();
    }
}
