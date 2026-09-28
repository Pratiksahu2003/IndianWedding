<?php

namespace App\Livewire\Studio\Packages;

use App\Models\Package;
use App\Support\Money;
use App\Support\Tenant;
use App\Support\YoutubeEmbed;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New service')]
class Create extends Component
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

    public string $youtube_url = '';

    public function save()
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
            'youtube_url' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! YoutubeEmbed::isValid($value)) {
                    $fail('Enter a valid YouTube link (watch, youtu.be, or shorts URL).');
                }
            }],
        ]);

        Package::query()->create([
            ...$data,
            'youtube_url' => trim($this->youtube_url) ?: null,
            'price' => Money::fromMajor($this->price),
            'organization_id' => Tenant::requireId(),
            'slug' => Str::slug($this->name).'-'.Str::lower(Str::random(4)),
            'is_active' => true,
            'is_public' => true,
        ]);

        session()->flash('status', 'Service created.');

        return $this->redirect(route('app.packages.index'), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', Package::class);

        return view('livewire.studio.packages.create');
    }
}
