<?php

namespace App\Livewire\Studio\Projects;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Project;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit project')]
class Edit extends Component
{
    public Project $project;
    public string $title = '';
    public string $description = '';
    public ?int $customer_id = null;
    public ?string $wedding_date = null;
    public string $venue = '';
    public string $city = '';
    public string $statusForm = 'booking_confirmed';

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project;
        $this->title = $project->title;
        $this->description = $project->notes ?? '';
        $this->customer_id = $project->customer_id;
        $this->wedding_date = $project->wedding_date?->toDateString();
        $this->venue = $project->venue ?? '';
        $this->city = $project->city ?? '';
        $this->statusForm = $project->status->value;
    }

    public function save()
    {
        $this->authorize('update', $this->project);
        $this->project->update($this->validatedPayload());
        session()->flash('status', 'Project updated.');

        return $this->redirect(route('app.projects.index'), navigate: true);
    }

    protected function validatedPayload(): array
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'wedding_date' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:120'],
            'statusForm' => ['required', Rule::enum(ProjectStatus::class)],
        ]);

        return [
            'title' => $data['title'],
            'notes' => $data['description'] ?: null,
            'customer_id' => $data['customer_id'],
            'wedding_date' => $data['wedding_date'],
            'venue' => $data['venue'] ?: null,
            'city' => $data['city'] ?: null,
            'status' => ProjectStatus::from($data['statusForm']),
        ];
    }

    public function render()
    {
        return view('livewire.studio.projects.edit', [
            'statuses' => ProjectStatus::cases(),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}