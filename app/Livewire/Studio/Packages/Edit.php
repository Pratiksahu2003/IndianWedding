<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use App\Support\Money;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit service')]
class Edit extends Component
{
    public Package $package;

    public string $name = '';

    public int $price = 0;

    public int $duration_hours = 8;

    public int $photographer_count = 1;

    public int $videographer_count = 0;

    public int $edited_photos = 200;

    public bool $includes_album = true;

    public bool $includes_video = false;

    public bool $includes_pre_wedding = false;

    public bool $includes_drone = false;

    public string $description = '';

    public function mount(Package $package): void
    {
        $this->authorize('update', $package);
        $this->package = $package;
        $this->name = $package->name;
        $this->price = (int) round($package->price / 100);
        $this->duration_hours = $package->duration_hours ?? 8;
        $this->photographer_count = $package->photographer_count ?? 1;
        $this->videographer_count = $package->videographer_count ?? 0;
        $this->edited_photos = $package->edited_photos ?? 200;
        $this->includes_album = (bool) $package->includes_album;
        $this->includes_video = (bool) $package->includes_video;
        $this->includes_pre_wedding = (bool) $package->includes_pre_wedding;
        $this->includes_drone = (bool) $package->includes_drone;
        $this->description = $package->description ?? '';
    }

    public function save()
    {
        $this->authorize('update', $this->package);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'photographer_count' => ['required', 'integer', 'min:0'],
            'videographer_count' => ['required', 'integer', 'min:0'],
            'edited_photos' => ['required', 'integer', 'min:0'],
            'includes_album' => ['boolean'],
            'includes_video' => ['boolean'],
            'includes_pre_wedding' => ['boolean'],
            'includes_drone' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $this->package->update([
            ...$data,
            'price' => Money::fromMajor($this->price),
        ]);

        session()->flash('status', 'Service updated.');

        return $this->redirect(route('app.packages.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.packages.edit');
    }
}
