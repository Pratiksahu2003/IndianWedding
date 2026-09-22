<?php

namespace App\Livewire\Studio\Payments;

use App\Actions\RecordPayment;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Support\Money;
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

    public bool $showForm = false;

    public ?int $milestone_id = null;

    public float|int|string $amount = 0;

    public string $notes = '';

    public function create(): void
    {
        $this->authorize('create', Payment::class);
        $this->reset('milestone_id', 'notes');
        $this->amount = 0;
        $this->showForm = true;
    }

    public function save(RecordPayment $record): void
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

        $this->showForm = false;
        $this->reset('milestone_id', 'notes');
        $this->amount = 0;
        session()->flash('status', 'Payment recorded.');
    }

    protected function openMilestones()
    {
        return PaymentMilestone::query()
            ->with('project')
            ->whereIn('status', [PaymentStatus::Due, PaymentStatus::Overdue, PaymentStatus::Pending, PaymentStatus::PartiallyPaid])
            ->orderBy('due_date')
            ->get();
    }

    public function delete(int $id): void
    {
        $payment = Payment::query()->findOrFail($id);
        $this->authorize('delete', $payment);
        $payment->delete();
        session()->flash('status', 'Payment removed.');
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->reset('milestone_id', 'notes');
        $this->amount = 0;
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
            'openMilestones' => $this->openMilestones(),
            'canCreate' => $user->can('create', Payment::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Payment::class),
        ]);
    }
}
