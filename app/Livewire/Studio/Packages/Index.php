<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Services')]
class Index extends Component
{
    public function delete(int $id): void
    {
        $package = Package::query()->findOrFail($id);
        $this->authorize('delete', $package);
        $package->delete();
        session()->flash('status', 'Service removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', Package::class);
        $user = auth()->user();

        return view('livewire.studio.packages.index', [
            'packages' => Package::query()->with('items')->orderBy('sort_order')->get(),
            'canCreate' => $user->can('create', Package::class),
            'canEdit' => $user->can('create', Package::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Package::class),
        ]);
    }
}
