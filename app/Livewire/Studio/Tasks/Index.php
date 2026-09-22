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
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $title = '';

    public ?int $project_id = null;

    public ?int $assigned_to = null;

    public string $priority = 'medium';

    public function create(): void
    {
        $this->authorize('create', ProjectTask::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $task = ProjectTask::query()->findOrFail($id);
        $this->authorize('update', $task);

        $this->editingId = $task->id;
        $this->title = $task->title;
        $this->project_id = $task->project_id;
        $this->assigned_to = $task->assigned_to;
        $this->priority = $task->priority->value;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
        ]);

        if ($this->editingId) {
            $task = ProjectTask::query()->findOrFail($this->editingId);
            $this->authorize('update', $task);
            $task->update([
                'title' => $data['title'],
                'project_id' => $data['project_id'],
                'assigned_to' => $data['assigned_to'],
                'priority' => TaskPriority::from($data['priority']),
            ]);
            session()->flash('status', 'Task updated.');
        } else {
            $this->authorize('create', ProjectTask::class);
            ProjectTask::query()->create([
                'organization_id' => Tenant::requireId(),
                'project_id' => $data['project_id'],
                'title' => $data['title'],
                'assigned_to' => $data['assigned_to'],
                'priority' => TaskPriority::from($data['priority']),
                'status' => TaskStatus::Todo,
            ]);
            session()->flash('status', 'Task created.');
        }

        $this->resetForm();
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
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'title', 'project_id', 'assigned_to');
        $this->priority = TaskPriority::Medium->value;
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
