<?php

namespace App\Livewire\Studio\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Payments')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $status = '';

    public function delete(int $id): void
    {
        $payment = Payment::query()->findOrFail($id);
        $this->authorize('delete', $payment);
        $payment->delete();
        session()->flash('status', 'Payment removed.');
    }

    public function render()
    {
        $this->authorize('viewAny', Payment::class);
        $user = auth()->user();

        return view('livewire.studio.payments.index', [
            'payments' => Payment::query()
                ->with(['project', 'customer'])
                ->when($this->status, fn ($q) => $q->where('status', $this->status))
                ->latest()
                ->paginate(20),
            'milestones' => PaymentMilestone::query()
                ->with('project')
                ->whereIn('status', [PaymentStatus::Due, PaymentStatus::Overdue, PaymentStatus::Pending, PaymentStatus::PartiallyPaid])
                ->orderBy('due_date')
                ->limit(12)
                ->get(),
            'canCreate' => $user->can('create', Payment::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Payment::class),
        ]);
    }
}
