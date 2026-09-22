<?php

namespace App\Livewire\Client;

use App\Models\Customer;
use App\Models\Project as WeddingProject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.client')]
class ProjectShow extends Component
{
    public WeddingProject $project;

    public function mount(WeddingProject $project): void
    {
        $this->authorize('view', $project);
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();
        abort_unless($project->customer_id === $customer->id, 403);
        $this->project = $project->load(['package', 'events', 'paymentMilestones', 'gallery', 'files', 'team.user', 'invoices']);
    }

    public function render()
    {
        return view('livewire.client.project')->title($this->project->title);
    }
}
