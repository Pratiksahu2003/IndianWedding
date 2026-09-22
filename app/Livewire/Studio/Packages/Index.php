<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use App\Support\Money;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Packages')]
class Index extends Component
{
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

    public function save(): void
    {
        $this->authorize('create', Package::class);
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
        Package::query()->create($data + ['price' => Money::fromMajor($this->price)]);
        $this->reset('name', 'description', 'price');
        session()->flash('status', 'Package created.');
    }

    public function render()
    {
        return view('livewire.studio.packages.index', [
            'packages' => Package::query()->with('items')->orderBy('sort_order')->get(),
        ]);
    }
}
