<?php

namespace App\Livewire\Studio\Packages\Concerns;

use App\Models\Package;
use App\Models\PackageImage;
use App\Support\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ManagesPackageImages
{
    /** @var array<int, TemporaryUploadedFile> */
    public array $newPhotos = [];

    public function updatedNewPhotos(): void
    {
        $this->uploadNewPhotos();
    }

    public function uploadNewPhotos(): void
    {
        if ($this->newPhotos === []) {
            return;
        }

        $this->authorize('update', $this->package);

        $this->validate([
            'newPhotos' => ['array'],
            'newPhotos.*' => ['image', 'max:4096'],
        ], [
            'newPhotos.*.max' => 'Each image must be 4 MB or smaller.',
            'newPhotos.*.image' => 'Only image files are allowed.',
        ]);

        $orgId = Tenant::requireId();
        $sort = (int) $this->package->images()->max('sort_order');
        $hasCover = $this->package->images()->where('is_cover', true)->exists()
            || filled($this->package->cover_image);

        foreach ($this->newPhotos as $photo) {
            $sort++;
            $filename = Str::uuid().'.'.$photo->getClientOriginalExtension();
            $path = $photo->storeAs(
                "packages/{$orgId}/{$this->package->id}",
                $filename,
                'public',
            );

            $isCover = ! $hasCover && $sort === 1;

            PackageImage::query()->create([
                'organization_id' => $orgId,
                'package_id' => $this->package->id,
                'path' => $path,
                'sort_order' => $sort,
                'is_cover' => $isCover,
            ]);

            if ($isCover) {
                $this->package->update(['cover_image' => Storage::disk('public')->url($path)]);
                $hasCover = true;
            }
        }

        $this->reset('newPhotos');
        $this->package->load('images');
        session()->flash('status', 'Images uploaded.');
    }

    public function deleteImage(int $imageId): void
    {
        $this->authorize('update', $this->package);

        $image = PackageImage::query()
            ->where('package_id', $this->package->id)
            ->findOrFail($imageId);

        Storage::disk('public')->delete($image->path);
        $wasCover = $image->is_cover;
        $image->delete();

        if ($wasCover) {
            $next = $this->package->images()->orderBy('sort_order')->first();
            if ($next) {
                $next->update(['is_cover' => true]);
                $this->package->update(['cover_image' => $next->url()]);
            } else {
                $this->package->update(['cover_image' => null]);
            }
        }

        $this->package->load('images');
        session()->flash('status', 'Image removed.');
    }

    public function setCoverImage(int $imageId): void
    {
        $this->authorize('update', $this->package);

        $image = PackageImage::query()
            ->where('package_id', $this->package->id)
            ->findOrFail($imageId);

        $this->package->images()->update(['is_cover' => false]);
        $image->update(['is_cover' => true]);
        $this->package->update(['cover_image' => $image->url()]);
        $this->package->load('images');

        session()->flash('status', 'Cover image updated.');
    }

    protected function storePhotosForNewPackage(Package $package, array $photos): void
    {
        if ($photos === []) {
            return;
        }

        $this->validate([
            'newPhotos' => ['array'],
            'newPhotos.*' => ['image', 'max:4096'],
        ], [
            'newPhotos.*.max' => 'Each image must be 4 MB or smaller.',
            'newPhotos.*.image' => 'Only image files are allowed.',
        ]);

        $orgId = Tenant::requireId();
        $sort = 0;

        foreach ($photos as $photo) {
            $sort++;
            $filename = Str::uuid().'.'.$photo->getClientOriginalExtension();
            $path = $photo->storeAs(
                "packages/{$orgId}/{$package->id}",
                $filename,
                'public',
            );

            PackageImage::query()->create([
                'organization_id' => $orgId,
                'package_id' => $package->id,
                'path' => $path,
                'sort_order' => $sort,
                'is_cover' => $sort === 1,
            ]);

            if ($sort === 1) {
                $package->update(['cover_image' => Storage::disk('public')->url($path)]);
            }
        }
    }
}
