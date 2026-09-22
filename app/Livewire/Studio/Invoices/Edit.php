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
#[Title('Edit invoice')]
class Edit extends Component
{
    use ManagesInvoiceForm;

    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->authorize('update', $invoice);
        $this->invoice = $invoice->load('items');
        $this->fillFromInvoice($this->invoice);
    }

    public function save()
    {
        $this->authorize('update', $this->invoice);
        $this->persistInvoice($this->invoice);
        session()->flash('status', 'Invoice updated.');

        return $this->redirect(route('app.invoices.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.studio.invoices.edit', [
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'city']),
            'projects' => Project::query()->orderBy('title')->get(['id', 'title', 'customer_id']),
            'statuses' => InvoiceStatus::cases(),
            'organization' => Organization::query()->find(Tenant::id()),
            'isEdit' => true,
        ]);
    }
}
