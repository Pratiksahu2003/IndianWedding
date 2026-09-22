<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use App\Support\Money;
use App\Support\Tenant;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Services')]
class Index extends Component
{
    public bool $showForm = true;

    public ?int $editingId = null;

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

    public function create(): void
    {
        $this->authorize('create', Package::class);
        $this->resetFormFields();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $package = Package::query()->findOrFail($id);
        $this->authorize('update', $package);

        $this->editingId = $package->id;
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
        $this->showForm = true;
    }

    public function save(): void
    {
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

        $payload = [
            ...$data,
            'price' => Money::fromMajor($this->price),
        ];

        if ($this->editingId) {
            $package = Package::query()->findOrFail($this->editingId);
            $this->authorize('update', $package);
            $package->update($payload);
            session()->flash('status', 'Package updated.');
        } else {
            $this->authorize('create', Package::class);
            Package::query()->create([
                ...$payload,
                'organization_id' => Tenant::requireId(),
                'slug' => Str::slug($this->name).'-'.Str::lower(Str::random(4)),
                'is_active' => true,
                'is_public' => true,
            ]);
            session()->flash('status', 'Package created.');
        }

        $this->resetFormFields();
    }

    public function delete(int $id): void
    {
        $package = Package::query()->findOrFail($id);
        $this->authorize('delete', $package);
        $package->delete();
        session()->flash('status', 'Package removed.');
    }

    public function cancel(): void
    {
        $this->resetFormFields();
    }

    protected function resetFormFields(): void
    {
        $this->reset(
            'editingId',
            'name',
            'description',
            'price',
            'includes_video',
            'includes_pre_wedding',
            'includes_drone',
        );
        $this->duration_hours = 8;
        $this->photographer_count = 1;
        $this->videographer_count = 0;
        $this->edited_photos = 200;
        $this->includes_album = true;
        $this->showForm = true;
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
