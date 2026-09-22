<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Events\LeadCreated;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadFollowup;
use App\Models\LeadSource;
use App\Models\Organization;
use App\Services\ActivityLogger;
use App\Support\Identifiers;
use Illuminate\Support\Facades\DB;

class CaptureLead
{
    public function __construct(protected ActivityLogger $activity) {}

    public function handle(Organization $organization, array $data): Lead
    {
        return DB::transaction(function () use ($organization, $data) {
            $source = LeadSource::query()->firstOrCreate(
                [
                    'organization_id' => $organization->id,
                    'slug' => $data['source'] ?? 'website',
                ],
                ['name' => ucfirst($data['source'] ?? 'Website')],
            );

            $lead = Lead::query()->create([
                'organization_id' => $organization->id,
                'lead_source_id' => $source->id,
                'lead_number' => Identifiers::lead($organization->id),
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? $data['phone'] ?? null,
                'wedding_date' => $data['wedding_date'] ?? null,
                'venue' => $data['venue'] ?? null,
                'city' => $data['city'] ?? null,
                'guest_count' => $data['guest_count'] ?? null,
                'budget' => isset($data['budget']) ? (int) $data['budget'] : null,
                'services' => $data['services'] ?? [],
                'package_id' => $data['package_id'] ?? null,
                'message' => $data['message'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => LeadStatus::New,
                'assigned_to' => $data['assigned_to'] ?? $organization->users()->wherePivot('role', 'studio_admin')->value('users.id'),
                'follow_up_at' => now()->addDay(),
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
            ]);

            LeadActivity::query()->create([
                'organization_id' => $organization->id,
                'lead_id' => $lead->id,
                'type' => 'created',
                'body' => 'Lead captured from '.($data['source'] ?? 'website'),
            ]);

            LeadFollowup::query()->create([
                'organization_id' => $organization->id,
                'lead_id' => $lead->id,
                'due_at' => now()->addDay(),
                'notes' => 'Initial follow-up',
                'status' => 'pending',
            ]);

            $this->activity->log('lead.created', $lead, ['source' => $data['source'] ?? 'website']);

            event(new LeadCreated($lead));

            return $lead;
        });
    }
}
