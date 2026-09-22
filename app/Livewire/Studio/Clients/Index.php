<?php

namespace App\Livewire\Studio\Clients;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Clients')]
class Index extends Component
{
    use WithPagination;
    #[Url] public string $search = '';

    public function render()
    {
        $this->authorize('viewAny', Customer::class);
        $clients = Customer::query()
            ->withCount('projects')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(15);

        return view('livewire.studio.clients.index', compact('clients'));
    }
}
