<?php

namespace App\Livewire\Studio\Production;

use App\Models\ProductionProject;
use App\Models\ProductionProjectImage;
use App\Support\YoutubeEmbed;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.studio')]
#[Title('Edit production project')]
class Edit extends Component
{
    use WithFileUploads;

    public ProductionProject $project;

    public string $name = '';

    public string $subtitle = '';

    public string $description = '';

    public string $body = '';

    public string $youtube_url = '';

    public string $category = '';

    public string $client_name = '';

    public string $location = '';

    public string $project_date = '';

    public bool $is_public = true;

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    public function mount(ProductionProject $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project->load('images');
        $this->name = $project->name;
        $this->subtitle = $project->subtitle ?? '';
        $this->description = $project->description ?? '';
        $this->body = $project->body ?? '';
        $this->youtube_url = $project->youtube_url ?? '';
        $this->category = $project->category ?? '';
        $this->client_name = $project->client_name ?? '';
        $this->location = $project->location ?? '';
        $this->project_date = optional($project->project_date)?->format('Y-m-d') ?? '';
        $this->is_public = (bool) $project->is_public;
    }

    public function uploadImages(): void
    {
        $this->authorize('update', $this->project);

        $this->validate([
            'newImages' => ['required', 'array', 'min:1'],
            'newImages.*' => ['required', 'image', 'max:4096'],
        ], [
            'newImages.*.max' => 'Each image must be 4 MB or smaller.',
        ]);

        $sortOrder = (int) $this->project->images()->max('sort_order');

        foreach ($this->newImages as $image) {
            $sortOrder++;
            $path = $image->storeAs(
                sprintf('org/%s/production/%s', $this->project->organization_id, $this->project->id),
                Str::uuid().'.'.$image->getClientOriginalExtension(),
                'public',
            );

            $this->project->images()->create([
                'organization_id' => $this->project->organization_id,
                'path' => $path,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->syncCoverImage();
        $this->reset('newImages');
        $this->project->load('images');
        session()->flash('status', 'Images uploaded.');
    }

    public function removeImage(int $imageId): void
    {
        $this->authorize('update', $this->project);

        $image = $this->project->images()->findOrFail($imageId);

        if (! str_starts_with($image->path, 'http://') && ! str_starts_with($image->path, 'https://')) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();
        $this->syncCoverImage();
        $this->project->load('images');
        session()->flash('status', 'Image removed.');
    }

    public function save()
    {
        $this->authorize('update', $this->project);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! YoutubeEmbed::isValid($value)) {
                    $fail('Enter a valid YouTube link.');
                }
            }],
            'category' => ['nullable', 'string', 'max:80'],
            'client_name' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'project_date' => ['nullable', 'date'],
            'is_public' => ['boolean'],
        ]);

        $this->project->update([
            ...$data,
            'youtube_url' => trim($this->youtube_url) ?: null,
            'project_date' => $this->project_date ?: null,
        ]);

        session()->flash('status', 'Production project saved.');

        return $this->redirect(route('app.production.index'), navigate: true);
    }

    protected function syncCoverImage(): void
    {
        $first = $this->project->images()->orderBy('sort_order')->first();

        $this->project->update([
            'cover_image' => $first?->url(),
        ]);
    }

    public function render()
    {
        return view('livewire.studio.production.edit');
    }
}
