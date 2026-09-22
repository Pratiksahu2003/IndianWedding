<?php

namespace App\Livewire\Studio\Leads;

use App\Actions\CaptureLead;
use App\Models\Organization;
use App\Models\Package;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New lead')]
class Form extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $whatsapp = '';
    public ?string $wedding_date = null;
    public string $venue = '';
    public string $city = '';
    public ?int $guest_count = null;
    public ?int $budget = null;
    public ?int $package_id = null;
    public string $notes = '';

    public function save(CaptureLead $action)
    {
        $this->authorize('create', \App\Models\Lead::class);
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'wedding_date' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:120'],
            'guest_count' => ['nullable', 'integer'],
            'budget' => ['nullable', 'integer'],
            'package_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $org = Tenant::current() ?? Organization::query()->findOrFail(Tenant::soleOrganizationId() ?? session('current_organization_id'));
        $lead = $action->handle($org, $data + ['source' => 'manual', 'assigned_to' => auth()->id()]);

        return redirect()->route('app.leads.show', $lead);
    }

    public function render()
    {
        return view('livewire.studio.leads.form', [
            'packages' => Package::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
