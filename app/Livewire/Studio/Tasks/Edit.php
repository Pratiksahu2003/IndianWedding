<?php

namespace App\Livewire\Studio\Tasks;

use App\Enums\TaskPriority;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit task')]
class Edit extends Component
{
    public ProjectTask $task;

    public string $title = '';

    public string $description = '';

    public ?int $project_id = null;

    public ?int $assigned_to = null;

    public string $priority = 'medium';

    public ?string $deadline = null;

    public function mount(ProjectTask $task): void
    {
        $this->authorize('update', $task);
        $this->task = $task;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->project_id = $task->project_id;
        $this->assigned_to = $task->assigned_to;
        $this->priority = $task->priority->value;
        $this->deadline = $task->deadline?->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->authorize('update', $this->task);
        $this->task->update($this->validatedPayload());
        session()->flash('status', 'Task updated.');

        return $this->redirect(route('app.tasks.index'), navigate: true);
    }

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

    public function render()
    {
        return view('livewire.studio.tasks.edit', [
            'projects' => Project::query()->orderBy('title')->get(['id', 'title']),
            'staff' => User::query()
                ->whereHas('memberships', fn ($q) => $q->where('organization_id', Tenant::id()))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}
