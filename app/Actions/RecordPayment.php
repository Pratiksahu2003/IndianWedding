<?php

namespace App\Actions;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Events\PaymentReceived;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Models\PaymentTransaction;
use App\Services\ActivityLogger;
use App\Services\AuditLogger;
use App\Support\Identifiers;
use Illuminate\Support\Facades\DB;

class RecordPayment
{
    public function __construct(
        protected ActivityLogger $activity,
        protected AuditLogger $audit,
    ) {}

    public function handle(PaymentMilestone $milestone, int $amount, array $meta = []): Payment
    {
        return DB::transaction(function () use ($milestone, $amount, $meta) {
            $payment = Payment::query()->create([
                'organization_id' => $milestone->organization_id,
                'project_id' => $milestone->project_id,
                'invoice_id' => $milestone->invoice_id,
                'milestone_id' => $milestone->id,
                'customer_id' => $milestone->project?->customer_id,
                'reference' => Identifiers::payment(),
                'amount' => $amount,
                'currency' => $meta['currency'] ?? 'INR',
                'gateway' => $meta['gateway'] ?? 'manual',
                'status' => PaymentStatus::Paid,
                'provider_payment_id' => $meta['provider_payment_id'] ?? null,
                'provider_payload' => $meta['payload'] ?? null,
                'paid_at' => now(),
                'notes' => $meta['notes'] ?? null,
            ]);

            PaymentTransaction::query()->create([
                'organization_id' => $milestone->organization_id,
                'payment_id' => $payment->id,
                'type' => 'charge',
                'amount' => $amount,
                'status' => 'paid',
                'payload' => $meta,
            ]);

            $paid = $milestone->paid_amount + $amount;
            $status = $paid >= $milestone->amount
                ? PaymentStatus::Paid
                : PaymentStatus::PartiallyPaid;

            $milestone->update([
                'paid_amount' => $paid,
                'status' => $status,
                'paid_at' => $status === PaymentStatus::Paid ? now() : $milestone->paid_at,
            ]);

            if ($invoice = $milestone->invoice) {
                $invoicePaid = $invoice->paid + $amount;
                $invoice->update([
                    'paid' => $invoicePaid,
                    'status' => $invoicePaid >= $invoice->total ? InvoiceStatus::Paid : InvoiceStatus::PartiallyPaid,
                ]);
            }

            $this->activity->log('payment.received', $payment, ['amount' => $amount]);
            $this->audit->log('payment.received', $payment, [], $payment->toArray());

            event(new PaymentReceived($payment));

            return $payment;
        });
    }
}
