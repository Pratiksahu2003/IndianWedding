<?php

namespace App\Livewire\Studio\Clients;

use App\Models\Customer;
use App\Support\Tenant;
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

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $client = Customer::query()->findOrFail($id);
        $this->authorize('delete', $client);
        $client->delete();
        session()->flash('status', 'Client removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', Customer::class);

        $user = auth()->user();

        return view('livewire.studio.clients.index', [
            'clients' => Customer::query()
                ->withCount('projects')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('customer_number', 'like', '%'.$this->search.'%');
                }))
                ->latest()
                ->paginate(15),
            'canCreate' => $user->can('create', Customer::class),
            'canEdit' => $user->can('create', Customer::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Customer::class),
        ]);
    }
}
