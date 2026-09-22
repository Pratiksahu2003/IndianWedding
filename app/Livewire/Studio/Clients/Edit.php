<?php

namespace App\Livewire\Studio\Clients;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Edit client')]
class Edit extends Component
{
    public Customer $client;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $city = '';

    public ?string $wedding_date = null;

    public string $notes = '';

    public function mount(Customer $client): void
    {
        $this->authorize('update', $client);
        $this->client = $client;
        $this->name = $client->name;
        $this->email = $client->email ?? '';
        $this->phone = $client->phone ?? '';
        $this->city = $client->city ?? '';
        $this->wedding_date = $client->wedding_date?->toDateString();
        $this->notes = $client->notes ?? '';
    }

    public function save()
    {
        $this->authorize('update', $this->client);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:80'],
            'wedding_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->client->update($data);
        session()->flash('status', 'Client updated.');

        return $this->redirect(route('app.clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.clients.edit');
    }
}
