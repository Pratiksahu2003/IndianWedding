<?php

namespace App\Livewire\Studio\Clients;

use App\Models\Customer;
use App\Support\Identifiers;
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

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $city = '';

    public ?string $wedding_date = null;

    public string $notes = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('create', Customer::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $client = Customer::query()->findOrFail($id);
        $this->authorize('update', $client);

        $this->editingId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email ?? '';
        $this->phone = $client->phone ?? '';
        $this->city = $client->city ?? '';
        $this->wedding_date = $client->wedding_date?->toDateString();
        $this->notes = $client->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:80'],
            'wedding_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($this->editingId) {
            $client = Customer::query()->findOrFail($this->editingId);
            $this->authorize('update', $client);
            $client->update($data);
            session()->flash('status', 'Client updated.');
        } else {
            $this->authorize('create', Customer::class);
            Customer::query()->create([
                ...$data,
                'organization_id' => Tenant::requireId(),
                'customer_number' => Identifiers::customer(Tenant::requireId()),
            ]);
            session()->flash('status', 'Client created.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $client = Customer::query()->findOrFail($id);
        $this->authorize('delete', $client);
        $client->delete();
        session()->flash('status', 'Client removed.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'name', 'email', 'phone', 'city', 'wedding_date', 'notes');
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
