<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Events\BookingConfirmed;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\PaymentMilestone;
use App\Models\Project;
use App\Models\ProjectEvent;
use App\Models\ProjectMilestone;
use App\Services\ActivityLogger;
use App\Support\Identifiers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConvertLeadToBooking
{
    public function __construct(protected ActivityLogger $activity) {}

    public function handle(Lead $lead, array $data = []): Project
    {
        return DB::transaction(function () use ($lead, $data) {
            $organization = $lead->organization;
            $customer = $lead->customer ?: Customer::query()->create([
                'organization_id' => $organization->id,
                'customer_number' => Identifiers::customer($organization->id),
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'whatsapp' => $lead->whatsapp,
                'wedding_date' => $lead->wedding_date,
                'city' => $lead->city,
            ]);

            $package = $lead->package;
            $total = (int) ($data['total_amount'] ?? $package?->price ?? 0);

            $project = Project::query()->create([
                'organization_id' => $organization->id,
                'customer_id' => $customer->id,
                'lead_id' => $lead->id,
                'package_id' => $lead->package_id,
                'project_number' => Identifiers::project($organization->id),
                'title' => $data['title'] ?? ($lead->name.' Wedding'),
                'wedding_date' => $lead->wedding_date,
                'venue' => $lead->venue,
                'city' => $lead->city,
                'total_amount' => $total,
                'status' => ProjectStatus::BookingConfirmed,
                'notes' => $data['notes'] ?? $lead->notes,
                'booked_at' => now(),
            ]);

            ProjectEvent::query()->create([
                'organization_id' => $organization->id,
                'project_id' => $project->id,
                'type' => 'wedding',
                'title' => 'Wedding',
                'date' => $lead->wedding_date,
                'venue' => $lead->venue,
            ]);

            foreach (ProjectStatus::cases() as $index => $status) {
                ProjectMilestone::query()->create([
                    'organization_id' => $organization->id,
                    'project_id' => $project->id,
                    'key' => $status->value,
                    'label' => $status->label(),
                    'sort_order' => $index,
                    'completed_at' => $status === ProjectStatus::BookingConfirmed ? now() : null,
                ]);
            }

            $invoiceNumber = Identifiers::invoice($organization->invoice_prefix, $organization->invoice_next_number);
            $organization->increment('invoice_next_number');

            $invoice = Invoice::query()->create([
                'organization_id' => $organization->id,
                'project_id' => $project->id,
                'customer_id' => $customer->id,
                'invoice_number' => $invoiceNumber,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => $total,
                'total' => $total,
                'status' => 'sent',
            ]);

            InvoiceItem::query()->create([
                'organization_id' => $organization->id,
                'invoice_id' => $invoice->id,
                'description' => $package?->name ?? 'Wedding photography package',
                'quantity' => 1,
                'unit_amount' => $total,
                'amount' => $total,
            ]);

            foreach ($organization->defaultMilestones() as $index => $milestone) {
                $amount = (int) round($total * ($milestone['percentage'] / 100));
                PaymentMilestone::query()->create([
                    'organization_id' => $organization->id,
                    'project_id' => $project->id,
                    'invoice_id' => $index === 0 ? $invoice->id : null,
                    'name' => $milestone['name'],
                    'percentage' => $milestone['percentage'],
                    'amount' => $amount,
                    'due_condition' => $milestone['due_condition'] ?? null,
                    'due_date' => $index === 0 ? now()->toDateString() : optional($lead->wedding_date)?->toDateString(),
                    'status' => $index === 0 ? PaymentStatus::Due : PaymentStatus::Pending,
                    'sort_order' => $index,
                ]);
            }

            Contract::query()->create([
                'organization_id' => $organization->id,
                'project_id' => $project->id,
                'title' => 'Photography Agreement',
                'body' => 'Standard wedding photography agreement for '.$project->title,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $lead->update([
                'customer_id' => $customer->id,
                'status' => LeadStatus::Booked,
                'converted_at' => now(),
            ]);

            $this->activity->log('lead.converted', $project, ['lead_id' => $lead->id]);

            event(new BookingConfirmed($project));

            return $project->fresh(['paymentMilestones', 'customer', 'package']);
        });
    }
}
