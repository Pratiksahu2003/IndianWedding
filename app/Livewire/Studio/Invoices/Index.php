<?php

namespace App\Livewire\Studio\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Project;
use App\Support\Gst;
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

    public string $description = 'Wedding photography & cinematography package';

    public string $hsn_sac = Gst::DEFAULT_SAC;

    public int $gst_rate = Gst::DEFAULT_RATE;

    public int $quantity = 1;

    public float|int|string $amount = 0;

    public float|int|string $discount = 0;

    public string $place_of_supply = '';

    public string $billing_gstin = '';

    public string $notes = '';

    public function mount(): void
    {
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(7)->toDateString();
    }

    public function updatedCustomerId($value): void
    {
        if (! $value) {
            return;
        }

        $customer = Customer::query()->find($value);
        if ($customer) {
            $this->place_of_supply = Gst::stateFromCity($customer->city) ?: ($customer->city ?: '');
        }
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
        $this->description = $item?->description ?? 'Wedding photography & cinematography package';
        $this->hsn_sac = $item?->hsn_sac ?: Gst::DEFAULT_SAC;
        $this->gst_rate = (int) ($item?->gst_rate ?: Gst::DEFAULT_RATE);
        $this->quantity = max(1, (int) ($item?->quantity ?: 1));
        $this->amount = ($item?->unit_amount ?? $invoice->subtotal ?? 0) / 100;
        $this->discount = ($invoice->discount ?? 0) / 100;
        $this->place_of_supply = $invoice->place_of_supply ?? '';
        $this->billing_gstin = $invoice->billing_gstin ?? '';
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
            'hsn_sac' => ['required', 'string', 'max:16'],
            'gst_rate' => ['required', 'integer', 'in:0,5,12,18,28'],
            'quantity' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'place_of_supply' => ['nullable', 'string', 'max:120'],
            'billing_gstin' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        /** @var Organization $org */
        $org = Organization::query()->findOrFail(Tenant::requireId());
        $customer = Customer::query()->findOrFail($data['customer_id']);

        $unit = Money::fromMajor($data['amount']);
        $qty = (int) $data['quantity'];
        $line = $unit * $qty;
        $discount = Money::fromMajor($data['discount'] ?? 0);
        $taxable = max(0, $line - $discount);
        $interstate = Gst::isInterstate($org, $customer);
        $gst = Gst::calculate($taxable, (int) $data['gst_rate'], $interstate);

        $place = $data['place_of_supply']
            ?: (Gst::stateFromCity($customer->city) ?: $customer->city);

        DB::transaction(function () use ($data, $org, $customer, $unit, $qty, $line, $discount, $taxable, $gst, $interstate, $place): void {
            $payload = [
                'customer_id' => $data['customer_id'],
                'project_id' => $data['project_id'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'status' => InvoiceStatus::from($data['status']),
                'subtotal' => $line,
                'discount' => $discount,
                'tax' => $gst['tax'],
                'total' => $gst['total'],
                'notes' => $data['notes'] ?: null,
                'place_of_supply' => $place,
                'billing_name' => $customer->name,
                'billing_gstin' => $data['billing_gstin'] ?: null,
                'billing_address' => trim(collect([
                    $customer->city,
                    Gst::stateFromCity($customer->city),
                ])->filter()->implode(', ')),
                'is_interstate' => $interstate,
            ];

            $itemPayload = [
                'description' => $data['description'],
                'hsn_sac' => $data['hsn_sac'],
                'gst_rate' => (int) $data['gst_rate'],
                'quantity' => $qty,
                'unit_amount' => $unit,
                'amount' => $line,
                'taxable_amount' => $taxable,
                'cgst_amount' => $gst['cgst'],
                'sgst_amount' => $gst['sgst'],
                'igst_amount' => $gst['igst'],
            ];

            if ($this->editingId) {
                $invoice = Invoice::query()->findOrFail($this->editingId);
                $this->authorize('update', $invoice);
                $invoice->update($payload);

                $item = $invoice->items()->first();
                if ($item) {
                    $item->update($itemPayload);
                } else {
                    InvoiceItem::query()->create([
                        ...$itemPayload,
                        'organization_id' => $invoice->organization_id,
                        'invoice_id' => $invoice->id,
                    ]);
                }

                session()->flash('status', 'Invoice updated.');

                return;
            }

            $this->authorize('create', Invoice::class);

            $invoiceNumber = Identifiers::invoice($org->invoice_prefix ?: 'INV', (int) $org->invoice_next_number);
            $org->increment('invoice_next_number');

            $invoice = Invoice::query()->create([
                ...$payload,
                'organization_id' => $org->id,
                'invoice_number' => $invoiceNumber,
                'paid' => 0,
            ]);

            InvoiceItem::query()->create([
                ...$itemPayload,
                'organization_id' => $org->id,
                'invoice_id' => $invoice->id,
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
        $this->reset(
            'showForm',
            'editingId',
            'customer_id',
            'project_id',
            'notes',
            'place_of_supply',
            'billing_gstin',
        );
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(7)->toDateString();
        $this->status = InvoiceStatus::Draft->value;
        $this->description = 'Wedding photography & cinematography package';
        $this->hsn_sac = Gst::DEFAULT_SAC;
        $this->gst_rate = Gst::DEFAULT_RATE;
        $this->quantity = 1;
        $this->amount = 0;
        $this->discount = 0;
    }

    public function render()
    {
        $this->authorize('viewAny', Invoice::class);
        $user = auth()->user();

        return view('livewire.studio.invoices.index', [
            'invoices' => Invoice::query()->with(['customer', 'project'])->latest()->paginate(15),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'city']),
            'projects' => Project::query()->orderBy('title')->get(['id', 'title', 'customer_id']),
            'statuses' => InvoiceStatus::cases(),
            'organization' => Organization::query()->find(Tenant::id()),
            'canCreate' => $user->can('create', Invoice::class),
            'canEdit' => $user->can('create', Invoice::class),
            'canDelete' => $user->canDeleteInOrganization(Tenant::current()) && $user->can('create', Invoice::class),
        ]);
    }
}
