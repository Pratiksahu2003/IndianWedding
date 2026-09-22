<?php

namespace App\Livewire\Studio\Invoices;

use App\Models\Invoice;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Invoices')]
class Index extends Component
{
    use WithPagination;

    public function delete(int $id): void
    {
        $invoice = Invoice::query()->findOrFail($id);
        $this->authorize('delete', $invoice);
        $invoice->delete();
        session()->flash('status', 'Invoice removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', Invoice::class);
        $user = auth()->user();

        return view('livewire.studio.invoices.index', [
            'invoices' => Invoice::query()->with(['customer', 'project'])->latest()->paginate(15),
            'canCreate' => $user->can('create', Invoice::class),
            'canEdit' => $user->can('create', Invoice::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Invoice::class),
        ]);
    }
}
