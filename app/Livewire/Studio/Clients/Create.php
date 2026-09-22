<?php

namespace App\Livewire\Studio\Clients;

use App\Models\Customer;
use App\Support\Identifiers;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New client')]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $city = '';

    public ?string $wedding_date = null;

    public string $notes = '';

    public function save()
    {
        $this->authorize('create', Customer::class);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:80'],
            'wedding_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Customer::query()->create([
            ...$data,
            'organization_id' => Tenant::requireId(),
            'customer_number' => Identifiers::customer(Tenant::requireId()),
        ]);

        session()->flash('status', 'Client created.');

        return $this->redirect(route('app.clients.index'), navigate: true);
    }

    public function render()
    {
        $this->authorize('create', Customer::class);

        return view('livewire.studio.clients.create');
    }
}
