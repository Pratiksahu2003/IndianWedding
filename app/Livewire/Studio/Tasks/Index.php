<?php

namespace App\Livewire\Studio\Tasks;

use App\Enums\TaskStatus;
use App\Models\ProjectTask;
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
        $task->update([
            'status' => TaskStatus::from($status),
            'completed_at' => $status === 'completed' ? now() : null,
        ]);
    }

    public function render()
    {
        $columns = [];
        foreach (TaskStatus::cases() as $status) {
            $columns[$status->value] = ProjectTask::query()->with(['project', 'assignee'])->where('status', $status)->orderBy('deadline')->get();
        }

        return view('livewire.studio.tasks.index', compact('columns'));
    }
}
