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

    /** @var null|'create'|'edit' */
    public ?string $formMode = null;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public ?int $customer_id = null;

    public ?string $wedding_date = null;

    public string $venue = '';

    public string $city = '';

    public string $statusForm = 'booking_confirmed';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create', Project::class);
        $this->resetFormFields();
        $this->formMode = 'create';
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $project = Project::query()->findOrFail($id);
        $this->authorize('update', $project);

        $this->formMode = 'edit';
        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->description = $project->notes ?? '';
        $this->customer_id = $project->customer_id;
        $this->wedding_date = $project->wedding_date?->toDateString();
        $this->venue = $project->venue ?? '';
        $this->city = $project->city ?? '';
        $this->statusForm = $project->status->value;
    }

    public function store(): void
    {
        $this->authorize('create', Project::class);
        $data = $this->validatedPayload();

        Project::query()->create([
            ...$data,
            'organization_id' => Tenant::requireId(),
            'project_number' => Identifiers::project(Tenant::requireId()),
            'booked_at' => now(),
        ]);

        session()->flash('status', 'Project created.');
        $this->cancel();
    }

    public function update(): void
    {
        abort_unless($this->editingId, 404);
        $project = Project::query()->findOrFail($this->editingId);
        $this->authorize('update', $project);

        $project->update($this->validatedPayload());
        session()->flash('status', 'Project updated.');
        $this->cancel();
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
        $this->resetFormFields();
        $this->formMode = null;
        $this->editingId = null;
    }

    /**
     * @return array<string, mixed>
     */
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

    protected function resetFormFields(): void
    {
        $this->reset('title', 'description', 'customer_id', 'wedding_date', 'venue', 'city');
        $this->statusForm = ProjectStatus::BookingConfirmed->value;
        $this->resetErrorBag();
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
