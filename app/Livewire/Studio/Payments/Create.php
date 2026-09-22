<?php

namespace App\Livewire\Studio\Payments;

use App\Actions\RecordPayment;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Support\Money;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Record payment')]
class Create extends Component
{
    public ?int $milestone_id = null;

    public float|int|string $amount = 0;

    public string $notes = '';

    public function save(RecordPayment $record)
    {
        $this->authorize('create', Payment::class);

        if ($this->openMilestones()->isEmpty()) {
            $this->addError('milestone_id', 'No open payment milestones available. Create a project booking first.');

            return;
        }

        $data = $this->validate([
            'milestone_id' => ['required', 'integer', 'exists:payment_milestones,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $milestone = PaymentMilestone::query()->findOrFail($data['milestone_id']);
        $record->handle($milestone, Money::fromMajor($data['amount']), [
            'gateway' => 'manual',
            'notes' => $data['notes'] ?: null,
        ]);

        session()->flash('status', 'Payment recorded.');

        return $this->redirect(route('app.payments.index'), navigate: true);
    }

    protected function openMilestones()
    {
        return PaymentMilestone::query()
            ->with('project')
            ->whereIn('status', [PaymentStatus::Due, PaymentStatus::Overdue, PaymentStatus::Pending, PaymentStatus::PartiallyPaid])
            ->orderBy('due_date')
            ->get();
    }

    public function render()
    {
        $this->authorize('create', Payment::class);

        return view('livewire.studio.payments.create', [
            'openMilestones' => $this->openMilestones(),
        ]);
    }
}
