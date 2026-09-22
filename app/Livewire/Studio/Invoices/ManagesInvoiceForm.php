<?php

namespace App\Livewire\Studio\Invoices;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Support\Gst;
use App\Support\Identifiers;
use App\Support\Money;
use App\Support\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

trait ManagesInvoiceForm
{
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

    protected function initInvoiceDefaults(): void
    {
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(7)->toDateString();
        $this->status = InvoiceStatus::Draft->value;
        $this->hsn_sac = Gst::DEFAULT_SAC;
        $this->gst_rate = Gst::DEFAULT_RATE;
        $this->quantity = 1;
        $this->description = 'Wedding photography & cinematography package';
    }

    protected function fillFromInvoice(Invoice $invoice): void
    {
        $item = $invoice->items->first();
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
    }

    protected function validatedInvoiceData(): array
    {
        return $this->validate([
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
    }

    protected function persistInvoice(?Invoice $invoice = null): Invoice
    {
        $data = $this->validatedInvoiceData();
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

        return DB::transaction(function () use ($data, $org, $customer, $unit, $qty, $line, $discount, $taxable, $gst, $interstate, $place, $invoice) {
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

            if ($invoice) {
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

                return $invoice->fresh();
            }

            $invoiceNumber = Identifiers::invoice($org->invoice_prefix ?: 'INV', (int) $org->invoice_next_number);
            $org->increment('invoice_next_number');

            $created = Invoice::query()->create([
                ...$payload,
                'organization_id' => $org->id,
                'invoice_number' => $invoiceNumber,
                'paid' => 0,
            ]);

            InvoiceItem::query()->create([
                ...$itemPayload,
                'organization_id' => $org->id,
                'invoice_id' => $created->id,
            ]);

            return $created;
        });
    }
}
