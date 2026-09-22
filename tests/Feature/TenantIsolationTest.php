<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_leads_are_scoped_to_the_current_organization(): void
    {
        $alpha = Organization::factory()->create();
        $beta = Organization::factory()->create();

        Tenant::run($alpha->id, fn () => Lead::factory()->create([
            'organization_id' => $alpha->id,
            'name' => 'Alpha Couple',
        ]));
        Tenant::run($beta->id, fn () => Lead::factory()->create([
            'organization_id' => $beta->id,
            'name' => 'Beta Couple',
        ]));

        Tenant::set($alpha->id);
        $this->assertSame(['Alpha Couple'], Lead::query()->pluck('name')->all());

        Tenant::set($beta->id);
        $this->assertSame(['Beta Couple'], Lead::query()->pluck('name')->all());
    }

    public function test_user_cannot_open_another_tenant_lead(): void
    {
        $alpha = Organization::factory()->create();
        $beta = Organization::factory()->create();
        $user = User::factory()->create();
        $alpha->users()->attach($user->id, ['role' => 'studio_admin', 'accepted_at' => now()]);

        $foreign = Tenant::run($beta->id, fn () => Lead::factory()->create(['organization_id' => $beta->id]));

        $this->actingAs($user);
        $this->withSession(['current_organization_id' => $alpha->id])
            ->get(route('app.leads.show', $foreign))
            ->assertForbidden();
    }
}
