<?php

namespace App\Livewire\Studio\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Tasks')]
class Index extends Component
{
    /** @var null|'create'|'edit' */
    public ?string $formMode = null;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public ?int $project_id = null;

    public ?int $assigned_to = null;

    public string $priority = 'medium';

    public ?string $deadline = null;

    public function openCreate(): void
    {
        $this->authorize('create', ProjectTask::class);
        $this->resetFormFields();
        $this->formMode = 'create';
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $task = ProjectTask::query()->findOrFail($id);
        $this->authorize('update', $task);

        $this->formMode = 'edit';
        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->project_id = $task->project_id;
        $this->assigned_to = $task->assigned_to;
        $this->priority = $task->priority->value;
        $this->deadline = $task->deadline?->format('Y-m-d\TH:i');
    }

    public function store(): void
    {
        $this->authorize('create', ProjectTask::class);
        $data = $this->validatedPayload();

        ProjectTask::query()->create([
            ...$data,
            'organization_id' => Tenant::requireId(),
            'status' => TaskStatus::Todo,
        ]);

        session()->flash('status', 'Task created.');
        $this->cancel();
    }

    public function update(): void
    {
        abort_unless($this->editingId, 404);
        $task = ProjectTask::query()->findOrFail($this->editingId);
        $this->authorize('update', $task);

        $task->update($this->validatedPayload());
        session()->flash('status', 'Task updated.');
        $this->cancel();
    }

    public function move(int $id, string $status): void
    {
        $task = ProjectTask::query()->findOrFail($id);
        $this->authorize('update', $task);
        $task->update([
            'status' => TaskStatus::from($status),
            'completed_at' => $status === TaskStatus::Completed->value ? now() : null,
        ]);
    }

    public function delete(int $id): void
    {
        $task = ProjectTask::query()->findOrFail($id);
        $this->authorize('delete', $task);
        $task->delete();
        session()->flash('status', 'Task removed.');
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
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'deadline' => ['nullable', 'date'],
        ]);

        return [
            'title' => $data['title'],
            'description' => $data['description'] ?: null,
            'project_id' => $data['project_id'],
            'assigned_to' => $data['assigned_to'],
            'priority' => TaskPriority::from($data['priority']),
            'deadline' => $data['deadline'] ?: null,
        ];
    }

    protected function resetFormFields(): void
    {
        $this->reset('title', 'description', 'project_id', 'assigned_to', 'deadline');
        $this->priority = TaskPriority::Medium->value;
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('viewAny', ProjectTask::class);
        $user = auth()->user();

        $columns = [];
        foreach (TaskStatus::cases() as $status) {
            $columns[$status->value] = ProjectTask::query()
                ->with(['project', 'assignee'])
                ->where('status', $status)
                ->orderBy('deadline')
                ->get();
        }

        return view('livewire.studio.tasks.index', [
            'columns' => $columns,
            'projects' => Project::query()->orderBy('title')->get(['id', 'title']),
            'staff' => User::query()
                ->whereHas('memberships', fn ($q) => $q->where('organization_id', Tenant::id()))
                ->orderBy('name')
                ->get(['id', 'name']),
            'canCreate' => $user->can('create', ProjectTask::class),
            'canEdit' => $user->can('create', ProjectTask::class) || $user->canInOrganization('tasks.assigned', Tenant::current()),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', ProjectTask::class),
        ]);
    }
}
