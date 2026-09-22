<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.studio')]
class Show extends Component
{
    public Package $package;

    public function mount(Package $package): void
    {
        $this->authorize('view', $package);
        $this->package = $package->load(['items', 'addons']);
    }

    public function render()
    {
        return view('livewire.studio.packages.show', [
            'projects' => $this->package->projects()->with('customer')->latest()->limit(12)->get(),
        ])->title($this->package->name);
    }
}
