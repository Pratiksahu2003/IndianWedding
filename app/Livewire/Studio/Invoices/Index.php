<?php

namespace App\Livewire\Studio\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Project;
use App\Support\Identifiers;
use App\Support\Money;
use App\Support\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.studio')]
#[Title('Invoices')]
class Index extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $customer_id = null;

    public ?int $project_id = null;

    public string $issue_date = '';

    public ?string $due_date = null;

    public string $status = 'draft';

    public string $description = 'Photography package';

    public float|int|string $amount = 0;

    public string $notes = '';

    public function mount(): void
    {
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(7)->toDateString();
    }

    public function create(): void
    {
        $this->authorize('create', Invoice::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $invoice = Invoice::query()->with('items')->findOrFail($id);
        $this->authorize('update', $invoice);

        $item = $invoice->items->first();
        $this->editingId = $invoice->id;
        $this->customer_id = $invoice->customer_id;
        $this->project_id = $invoice->project_id;
        $this->issue_date = $invoice->issue_date?->toDateString() ?? now()->toDateString();
        $this->due_date = $invoice->due_date?->toDateString();
        $this->status = $invoice->status->value;
        $this->description = $item?->description ?? 'Photography package';
        $this->amount = ($invoice->total ?? 0) / 100;
        $this->notes = $invoice->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'customer_id' => [
                'required',
                'integer',
                Rule::exists('customers', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'project_id' => [
                'nullable',
                'integer',
                Rule::exists('projects', 'id')->where(fn ($q) => $q->where('organization_id', Tenant::requireId())),
            ],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::enum(InvoiceStatus::class)],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $total = Money::fromMajor($data['amount']);

        DB::transaction(function () use ($data, $total): void {
            if ($this->editingId) {
                $invoice = Invoice::query()->findOrFail($this->editingId);
                $this->authorize('update', $invoice);

                $invoice->update([
                    'customer_id' => $data['customer_id'],
                    'project_id' => $data['project_id'],
                    'issue_date' => $data['issue_date'],
                    'due_date' => $data['due_date'],
                    'status' => InvoiceStatus::from($data['status']),
                    'subtotal' => $total,
                    'total' => $total,
                    'notes' => $data['notes'] ?: null,
                ]);

                $item = $invoice->items()->first();
                if ($item) {
                    $item->update([
                        'description' => $data['description'],
                        'quantity' => 1,
                        'unit_amount' => $total,
                        'amount' => $total,
                    ]);
                } else {
                    InvoiceItem::query()->create([
                        'organization_id' => $invoice->organization_id,
                        'invoice_id' => $invoice->id,
                        'description' => $data['description'],
                        'quantity' => 1,
                        'unit_amount' => $total,
                        'amount' => $total,
                    ]);
                }

                session()->flash('status', 'Invoice updated.');

                return;
            }

            $this->authorize('create', Invoice::class);

            /** @var Organization $org */
            $org = Organization::query()->findOrFail(Tenant::requireId());
            $invoiceNumber = Identifiers::invoice($org->invoice_prefix ?: 'INV', (int) $org->invoice_next_number);
            $org->increment('invoice_next_number');

            $invoice = Invoice::query()->create([
                'organization_id' => $org->id,
                'customer_id' => $data['customer_id'],
                'project_id' => $data['project_id'],
                'invoice_number' => $invoiceNumber,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'subtotal' => $total,
                'total' => $total,
                'paid' => 0,
                'status' => InvoiceStatus::from($data['status']),
                'notes' => $data['notes'] ?: null,
            ]);

            InvoiceItem::query()->create([
                'organization_id' => $org->id,
                'invoice_id' => $invoice->id,
                'description' => $data['description'],
                'quantity' => 1,
                'unit_amount' => $total,
                'amount' => $total,
            ]);

            session()->flash('status', 'Invoice created.');
        });

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $invoice = Invoice::query()->findOrFail($id);
        $this->authorize('delete', $invoice);
        $invoice->delete();
        session()->flash('status', 'Invoice removed.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('showForm', 'editingId', 'customer_id', 'project_id', 'notes');
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(7)->toDateString();
        $this->status = InvoiceStatus::Draft->value;
        $this->description = 'Photography package';
        $this->amount = 0;
    }

    public function render()
    {
        $this->authorize('viewAny', Invoice::class);
        $user = auth()->user();

        return view('livewire.studio.invoices.index', [
            'invoices' => Invoice::query()->with(['customer', 'project'])->latest()->paginate(15),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'projects' => Project::query()->orderBy('title')->get(['id', 'title', 'customer_id']),
            'statuses' => InvoiceStatus::cases(),
            'canCreate' => $user->can('create', Invoice::class),
            'canEdit' => $user->can('create', Invoice::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Invoice::class),
        ]);
    }
}
