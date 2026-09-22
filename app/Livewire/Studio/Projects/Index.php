<?php

namespace App\Livewire\Studio\Projects;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Support\Tenant;
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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $project = Project::query()->findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
        session()->flash('status', 'Project removed.');
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
            'canCreate' => $user->can('create', Project::class),
            'canEdit' => $user->can('create', Project::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Project::class),
        ]);
    }
}
