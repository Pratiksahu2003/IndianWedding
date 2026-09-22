<?php

namespace Tests\Unit;

use App\Actions\RecordPayment;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\PaymentMilestone;
use App\Models\Project;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_and_full_milestone_payments(): void
    {
        $org = Organization::factory()->create();
        Tenant::set($org->id);
        $customer = Customer::factory()->create(['organization_id' => $org->id]);
        $project = Project::factory()->create([
            'organization_id' => $org->id,
            'customer_id' => $customer->id,
            'total_amount' => 10000,
        ]);
        $milestone = PaymentMilestone::query()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'name' => 'Advance',
            'percentage' => 50,
            'amount' => 5000,
            'status' => PaymentStatus::Due,
        ]);

        app(RecordPayment::class)->handle($milestone, 2000);
        $this->assertSame(PaymentStatus::PartiallyPaid, $milestone->fresh()->status);

        app(RecordPayment::class)->handle($milestone->fresh(), 3000);
        $this->assertSame(PaymentStatus::Paid, $milestone->fresh()->status);
        $this->assertSame(5000, $project->fresh()->paidAmount());
    }
}
