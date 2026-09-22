<?php

namespace App\Livewire\Studio\Leads;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Lead pipeline')]
class Pipeline extends Component
{
    public function move(int $leadId, string $status, ActivityLogger $logger): void
    {
        $lead = Lead::query()->findOrFail($leadId);
        $this->authorize('update', $lead);
        $from = $lead->status;
        $lead->update(['status' => LeadStatus::from($status)]);
        LeadActivity::query()->create([
            'organization_id' => $lead->organization_id,
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'status_changed',
            'body' => 'Status changed from '.$from->label().' to '.$lead->status->label(),
        ]);
        $logger->log('lead.status_changed', $lead, ['from' => $from->value, 'to' => $status]);
    }

    public function render()
    {
        $this->authorize('viewAny', Lead::class);
        $columns = [];
        foreach (LeadStatus::pipeline() as $status) {
            $columns[$status->value] = [
                'status' => $status,
                'leads' => Lead::query()->with('assignee')->where('status', $status)->latest()->limit(40)->get(),
            ];
        }

        return view('livewire.studio.leads.pipeline', compact('columns'));
    }
}
