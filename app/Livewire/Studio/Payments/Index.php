<?php

namespace App\Livewire\Studio\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentMilestone;
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
    #[Url] public string $status = '';

    public function render()
    {
        $this->authorize('viewAny', Payment::class);
        $payments = Payment::query()->with(['project', 'customer'])->when($this->status, fn ($q) => $q->where('status', $this->status))->latest()->paginate(20);
        $milestones = PaymentMilestone::query()->with('project')->whereIn('status', [PaymentStatus::Due, PaymentStatus::Overdue, PaymentStatus::Pending])->orderBy('due_date')->limit(12)->get();

        return view('livewire.studio.payments.index', compact('payments', 'milestones'));
    }
}
