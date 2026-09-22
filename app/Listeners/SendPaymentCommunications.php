<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Mail\PaymentReceivedMail;
use App\Services\CommunicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentCommunications implements ShouldQueue
{
    public function __construct(protected CommunicationService $communication) {}

    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment->load('customer');

        if ($payment->customer?->email) {
            Mail::to($payment->customer->email)->queue(new PaymentReceivedMail($payment));
        }

        $this->communication->notify($payment, 'payment_received', [
            'email' => $payment->customer?->email,
            'whatsapp' => $payment->customer?->whatsapp,
            'subject' => 'Payment received',
            'body' => 'We received your payment '.$payment->reference,
        ]);
    }
}
