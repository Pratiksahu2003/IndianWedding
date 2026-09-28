<?php

namespace App\Livewire\Studio\Production;

use App\Models\ProductionProject;
use App\Support\Tenant;
use App\Support\YoutubeEmbed;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New production project')]
class Create extends Component
{
    public string $name = '';

    public string $subtitle = '';

    public string $description = '';

    public string $youtube_url = '';

    public string $category = 'wedding-film';

    public string $client_name = '';

    public string $location = '';

    public function save()
    {
        $this->authorize('create', ProductionProject::class);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'string', 'max:500', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! YoutubeEmbed::isValid($value)) {
                    $fail('Enter a valid YouTube link.');
                }
            }],
            'category' => ['nullable', 'string', 'max:80'],
            'client_name' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);

        $project = ProductionProject::query()->create([
            ...$data,
            'youtube_url' => trim($this->youtube_url) ?: null,
            'organization_id' => Tenant::requireId(),
            'slug' => Str::slug($this->name).'-'.Str::lower(Str::random(4)),
            'is_active' => true,
            'is_public' => true,
            'sort_order' => (int) ProductionProject::query()->max('sort_order') + 1,
        ]);

        session()->flash('status', 'Production project created. Add photos and video next.');

        return $this->redirect(route('app.production.edit', $project), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', ProductionProject::class);

        return view('livewire.studio.production.create');
    }
}
