<?php

namespace App\Livewire\Studio\Packages;

use App\Livewire\Studio\Packages\Concerns\ManagesPackageFeatures;
use App\Livewire\Studio\Packages\Concerns\ManagesPackageIncludes;
use App\Models\Package;
use App\Support\Money;
use App\Support\PackageOptions;
use Illuminate\Validation\Rule;
use App\Support\YoutubeEmbed;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.studio')]
#[Title('Edit service')]
class Edit extends Component
{
    use ManagesPackageFeatures, ManagesPackageIncludes, WithFileUploads;

    public Package $package;

    public string $package_type = 'service';

    public string $name = '';

    public int $price = 0;

    public int $duration_hours = 8;

    public int $photographer_count = 1;

    public int $videographer_count = 0;

    public int $edited_photos = 200;

    public string $description = '';

    public string $youtube_url = '';

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    public function mount(Package $package): void
    {
        $this->authorize('update', $package);
        $this->package = $package->load(['images', 'items']);
        $this->package_type = $package->package_type ?? 'service';
        $this->featureItems = $package->items->pluck('name')->all();
        $this->name = $package->name;
        $this->price = (int) round($package->price / 100);
        $this->duration_hours = $package->duration_hours ?? 8;
        $this->photographer_count = $package->photographer_count ?? 1;
        $this->videographer_count = $package->videographer_count ?? 0;
        $this->edited_photos = $package->edited_photos ?? 200;
        $this->loadIncludesFromPackage($package);
        $this->description = $package->description ?? '';
        $this->youtube_url = $package->youtube_url ?? '';
    }

    public function uploadImages(): void
    {
        $this->authorize('update', $this->package);

        $this->validate([
            'newImages' => ['required', 'array', 'min:1'],
            'newImages.*' => ['required', 'image', 'max:4096'],
        ], [
            'newImages.*.max' => 'Each image must be 4 MB or smaller.',
            'newImages.*.image' => 'Only image files are allowed.',
        ]);

        $sortOrder = (int) $this->package->images()->max('sort_order');

        foreach ($this->newImages as $image) {
            $sortOrder++;
            $path = $image->store(
                sprintf('org/%s/packages/%s', $this->package->organization_id, $this->package->id),
                'public',
            );

            $this->package->images()->create([
                'organization_id' => $this->package->organization_id,
                'path' => $path,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->syncCoverImage();
        $this->reset('newImages');
        $this->package->load('images');
        session()->flash('status', 'Images uploaded.');
    }

    public function removeImage(int $imageId): void
    {
        $this->authorize('update', $this->package);

        $image = $this->package->images()->findOrFail($imageId);

        if (! str_starts_with($image->path, 'http://') && ! str_starts_with($image->path, 'https://')) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();
        $this->syncCoverImage();
        $this->package->load('images');
        session()->flash('status', 'Image removed.');
    }

    public function save()
    {
        $this->authorize('update', $this->package);

        $data = $this->validate([
            'package_type' => ['required', Rule::in(PackageOptions::typeKeys())],
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'photographer_count' => ['required', 'integer', 'min:0'],
            'videographer_count' => ['required', 'integer', 'min:0'],
            'edited_photos' => ['required', 'integer', 'min:0'],
            ...$this->includeValidationRules(),
            'description' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! YoutubeEmbed::isValid($value)) {
                    $fail('Enter a valid YouTube link (watch, youtu.be, or shorts URL).');
                }
            }],
        ]);

        $this->package->update([
            ...$data,
            'youtube_url' => trim($this->youtube_url) ?: null,
            'price' => Money::fromMajor($this->price),
        ]);

        $this->syncFeatureItems($this->package);

        session()->flash('status', 'Package updated.');

        return $this->redirect(route('app.packages.index'), navigate: true);
    }

    protected function syncCoverImage(): void
    {
        $firstImage = $this->package->images()->orderBy('sort_order')->first();

        $this->package->update([
            'cover_image' => $firstImage?->url(),
        ]);
    }

    public function render()
    {
        return view('livewire.studio.packages.edit');
    }
}
