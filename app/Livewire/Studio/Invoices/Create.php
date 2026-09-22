<?php

namespace App\Livewire\Studio\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Project;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('New invoice')]
class Create extends Component
{
    use ManagesInvoiceForm;

    public function mount(): void
    {
        $this->authorize('create', Invoice::class);
        $this->initInvoiceDefaults();
    }

    public function save()
    {
        $this->authorize('create', Invoice::class);
        $this->persistInvoice();
        session()->flash('status', 'Invoice created.');

        return $this->redirect(route('app.invoices.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.invoices.create', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'city']),
            'projects' => Project::query()->orderBy('title')->get(['id', 'title', 'customer_id']),
            'statuses' => InvoiceStatus::cases(),
            'organization' => Organization::query()->find(Tenant::id()),
            'isEdit' => false,
        ]);
    }
}
