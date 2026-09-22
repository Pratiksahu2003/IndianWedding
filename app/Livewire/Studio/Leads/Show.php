<?php

namespace App\Livewire\Studio\Leads;

use App\Actions\ConvertLeadToBooking;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadNote;
use App\Models\User;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.studio')]
class Show extends Component
{
    public Lead $lead;

    public string $note = '';

    public string $status;

    public ?string $follow_up_at = null;

    public function mount(Lead $lead): void
    {
        $this->authorize('view', $lead);
        $this->lead = $lead;
        $this->status = $lead->status->value;
        $this->follow_up_at = optional($lead->follow_up_at)?->format('Y-m-d\TH:i');
    }

    public function addNote(): void
    {
        $this->authorize('update', $this->lead);
        $this->validate(['note' => ['required', 'string', 'max:4000']]);

        LeadNote::query()->create([
            'organization_id' => $this->lead->organization_id ?: Tenant::requireId(),
            'lead_id' => $this->lead->id,
            'user_id' => auth()->id(),
            'body' => $this->note,
        ]);

        $this->note = '';
        $this->lead->refresh();
        session()->flash('status', 'Note saved.');
    }

    public function updateStatus(): void
    {
        $this->authorize('update', $this->lead);
        $this->validate([
            'status' => ['required', 'string'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $this->lead->update([
            'status' => LeadStatus::from($this->status),
            'follow_up_at' => $this->follow_up_at,
        ]);

        $this->lead->refresh();
        session()->flash('status', 'Lead updated.');
    }

    public function scheduleFollowup(): void
    {
        $this->authorize('update', $this->lead);
        $this->validate(['follow_up_at' => ['required', 'date']]);

        LeadFollowup::query()->create([
            'organization_id' => $this->lead->organization_id ?: Tenant::requireId(),
            'lead_id' => $this->lead->id,
            'user_id' => auth()->id(),
            'due_at' => $this->follow_up_at,
            'status' => 'pending',
        ]);

        $this->lead->update(['follow_up_at' => $this->follow_up_at]);
        $this->lead->refresh();
        session()->flash('status', 'Follow-up scheduled.');
    }

    public function convert(ConvertLeadToBooking $convert)
    {
        $this->authorize('update', $this->lead);
        $project = $convert->handle($this->lead);

        return redirect()->route('app.projects.show', $project);
    }

    public function render()
    {
        // Always re-query relations — Livewire rehydration exposes the text `notes`
        // column as $lead->notes (string), which breaks @foreach after form submits.
        $this->lead->refresh();

        return view('livewire.studio.leads.show', [
            'statuses' => LeadStatus::cases(),
            'staff' => User::query()
                ->whereHas('memberships', fn ($q) => $q->where('organization_id', Tenant::requireId()))
                ->orderBy('name')
                ->get(),
            'leadNotes' => $this->lead->leadNotes()->with('user')->latest()->get(),
            'leadActivities' => $this->lead->activities()->with('user')->latest()->get(),
        ])->title($this->lead->name);
    }
}
