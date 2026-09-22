<?php

namespace App\Livewire\Studio\Projects;

use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\Project;
use App\Support\Identifiers;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Projects')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $title = '';

    public ?int $customer_id = null;

    public ?string $wedding_date = null;

    public string $statusForm = 'booking_confirmed';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('create', Project::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $project = Project::query()->findOrFail($id);
        $this->authorize('update', $project);

        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->customer_id = $project->customer_id;
        $this->wedding_date = $project->wedding_date?->toDateString();
        $this->statusForm = $project->status->value;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'wedding_date' => ['nullable', 'date'],
            'statusForm' => ['required', Rule::enum(ProjectStatus::class)],
        ]);

        $payload = [
            'title' => $data['title'],
            'customer_id' => $data['customer_id'],
            'wedding_date' => $data['wedding_date'],
            'status' => ProjectStatus::from($data['statusForm']),
        ];

        if ($this->editingId) {
            $project = Project::query()->findOrFail($this->editingId);
            $this->authorize('update', $project);
            $project->update($payload);
            session()->flash('status', 'Project updated.');
        } else {
            $this->authorize('create', Project::class);
            Project::query()->create([
                ...$payload,
                'organization_id' => Tenant::requireId(),
                'project_number' => Identifiers::project(Tenant::requireId()),
                'booked_at' => now(),
            ]);
            session()->flash('status', 'Project created.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $project = Project::query()->findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
        session()->flash('status', 'Project removed.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'title', 'customer_id', 'wedding_date');
        $this->statusForm = ProjectStatus::BookingConfirmed->value;
    }

    public function render()
    {
        $this->authorize('viewAny', Project::class);
        $user = auth()->user();

        $projects = Project::query()
            ->with(['customer', 'package'])
            ->when(! $user->canInOrganization('projects.manage', Tenant::current()), function ($q) use ($user) {
                $q->whereHas('team', fn ($t) => $t->where('user_id', $user->id));
            })
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('project_number', 'like', '%'.$this->search.'%');
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(12);

        return view('livewire.studio.projects.index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::cases(),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'canCreate' => $user->can('create', Project::class),
            'canEdit' => $user->can('create', Project::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Project::class),
        ]);
    }
}
