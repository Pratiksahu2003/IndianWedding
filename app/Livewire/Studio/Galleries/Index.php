<?php

namespace App\Livewire\Studio\Galleries;

use App\Models\Gallery;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Galleries')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.studio.galleries.index', [
            'galleries' => Gallery::query()->with('project')->latest()->get(),
        ]);
    }
}
