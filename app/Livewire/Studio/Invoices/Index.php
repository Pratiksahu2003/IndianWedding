<?php

namespace App\Livewire\Studio\Invoices;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Invoices')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $this->authorize('viewAny', Invoice::class);

        return view('livewire.studio.invoices.index', [
            'invoices' => Invoice::query()->with(['customer', 'project'])->latest()->paginate(15),
        ]);
    }
}
