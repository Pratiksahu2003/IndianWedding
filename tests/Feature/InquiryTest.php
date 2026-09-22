<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Package;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_website_inquiry_creates_a_lead(): void
    {
        $org = Organization::factory()->create(['slug' => 'lumina-atelier']);
        Tenant::run($org->id, fn () => Package::factory()->create(['organization_id' => $org->id]));

        $this->post('/inquiry', [
            'name' => 'Riya Kapoor',
            'email' => 'riya@example.com',
            'phone' => '+919999999999',
            'city' => 'Jaipur',
            'message' => 'We are getting married in November.',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'email' => 'riya@example.com',
            'organization_id' => $org->id,
        ]);
    }

    public function test_honeypot_rejects_bots(): void
    {
        Organization::factory()->create(['slug' => 'lumina-atelier']);

        $this->post('/inquiry', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'phone' => '+919999999999',
            'website' => 'https://spam.test',
        ])->assertSessionHasErrors();
    }
}
