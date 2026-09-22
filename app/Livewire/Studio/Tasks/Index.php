<?php

namespace App\Livewire\Studio\Tasks;

use App\Enums\TaskStatus;
use App\Models\ProjectTask;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Tasks')]
class Index extends Component
{
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
            'canCreate' => $user->can('create', ProjectTask::class),
            'canEdit' => $user->can('create', ProjectTask::class) || $user->canInOrganization('tasks.assigned', Tenant::current()),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', ProjectTask::class),
        ]);
    }
}
