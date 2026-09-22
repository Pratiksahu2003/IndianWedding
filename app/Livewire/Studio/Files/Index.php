<?php

namespace App\Livewire\Studio\Files;

use App\Models\MediaFile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Files')]
class Index extends Component
{
    use WithPagination;
    #[Url] public string $kind = '';
    #[Url] public string $search = '';

    public function render()
    {
        $files = MediaFile::query()->with(['project', 'uploader'])
            ->when($this->kind, fn ($q) => $q->where('kind', $this->kind))
            ->when($this->search, fn ($q) => $q->where('original_name', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(20);

        return view('livewire.studio.files.index', compact('files'));
    }
}
