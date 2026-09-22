<?php

namespace App\Livewire\Studio\Settings;

use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Settings')]
class Index extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $timezone = 'Asia/Kolkata';
    public string $currency = 'INR';
    public string $invoice_prefix = 'INV';
    public string $tax_id = '';
    public string $milestones_json = '';

    public function mount(): void
    {
        $org = Tenant::current();
        abort_unless($org, 404);
        $this->name = $org->name;
        $this->email = (string) $org->email;
        $this->phone = (string) $org->phone;
        $this->timezone = $org->timezone;
        $this->currency = $org->currency;
        $this->invoice_prefix = $org->invoice_prefix;
        $this->tax_id = (string) $org->tax_id;
        $this->milestones_json = json_encode($org->defaultMilestones(), JSON_PRETTY_PRINT);
    }

    public function save(): void
    {
        $org = Tenant::current();
        $this->authorize('viewAny', \App\Models\Lead::class);
        abort_unless(auth()->user()->canInOrganization('settings.manage', $org), 403);
        $org->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
            'invoice_prefix' => $this->invoice_prefix,
            'tax_id' => $this->tax_id,
        ]);
        $decoded = json_decode($this->milestones_json, true);
        if (is_array($decoded)) {
            $org->settings()->update(['payment_milestones' => $decoded]);
        }
        session()->flash('status', 'Settings saved.');
    }

    public function render()
    {
        return view('livewire.studio.settings.index');
    }
}
