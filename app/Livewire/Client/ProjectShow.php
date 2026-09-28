<?php

namespace App\Livewire\Client;

use App\Models\Project as WeddingProject;
use App\Support\ClientPortal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.client')]
class ProjectShow extends Component
{
    public WeddingProject $project;

    public function mount(WeddingProject $project): void
    {
        $this->authorize('view', $project);
        $customer = ClientPortal::customer();
        abort_unless($customer && $project->customer_id === $customer->id, 403);
        $this->project = $project->load(['package', 'events', 'paymentMilestones', 'gallery', 'files', 'team.user', 'invoices']);
    }

    public function render()
    {
        return view('livewire.client.project')->title($this->project->title);
    }
}
