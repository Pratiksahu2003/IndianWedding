<?php

namespace App\Livewire\Studio\Production;

use App\Models\ProductionProject;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Production')]
class Index extends Component
{
    public function delete(int $id): void
    {
        $project = ProductionProject::query()->findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
        session()->flash('status', 'Production project removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', ProductionProject::class);
        $user = auth()->user();

        return view('livewire.studio.production.index', [
            'projects' => ProductionProject::query()->withCount('images')->orderBy('sort_order')->orderBy('name')->get(),
            'canCreate' => $user->can('create', ProductionProject::class),
            'canEdit' => $user->can('create', ProductionProject::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', ProductionProject::class),
        ]);
    }
}
