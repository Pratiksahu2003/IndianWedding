<?php

namespace Tests\Feature;

use App\Actions\ConvertLeadToBooking;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Package;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_converting_a_lead_creates_project_milestones_and_invoice(): void
    {
        $org = Organization::factory()->create();
        Tenant::set($org->id);
        $package = Package::factory()->create(['organization_id' => $org->id, 'price' => 10000000]);
        $lead = Lead::factory()->create([
            'organization_id' => $org->id,
            'package_id' => $package->id,
            'status' => LeadStatus::Negotiation,
        ]);

        $project = app(ConvertLeadToBooking::class)->handle($lead);

        $this->assertSame(LeadStatus::Booked, $lead->fresh()->status);
        $this->assertSame(10000000, $project->total_amount);
        $this->assertCount(3, $project->paymentMilestones);
        $this->assertDatabaseHas('invoices', ['project_id' => $project->id]);
        $this->assertEquals(50, $project->paymentMilestones->first()->percentage);
    }
}
