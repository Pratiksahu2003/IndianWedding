<?php

namespace App\Livewire\Studio\Leads;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Leads')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $source = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $lead = Lead::query()->findOrFail($id);
        $this->authorize('delete', $lead);
        $lead->delete();
        session()->flash('status', 'Lead archived.');
    }

    public function render()
    {
        $this->authorize('viewAny', Lead::class);

        $leads = Lead::query()
            ->with(['assignee', 'source', 'package'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('lead_number', 'like', '%'.$this->search.'%');
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->source, fn ($q) => $q->where('lead_source_id', $this->source))
            ->latest()
            ->paginate(12);

        return view('livewire.studio.leads.index', [
            'leads' => $leads,
            'statuses' => LeadStatus::cases(),
            'sources' => LeadSource::query()->orderBy('name')->get(),
        ]);
    }
}
