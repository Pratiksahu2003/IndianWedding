<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Mail\PaymentReminderMail;
use App\Models\PaymentMilestone;
use App\Services\CommunicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $milestoneId) {}

    public function handle(CommunicationService $communication): void
    {
        $milestone = PaymentMilestone::withoutTenant()->with('project.customer')->find($this->milestoneId);
        if (! $milestone || in_array($milestone->status, [PaymentStatus::Paid, PaymentStatus::Cancelled], true)) {
            return;
        }

        $customer = $milestone->project?->customer;
        if ($customer?->email) {
            Mail::to($customer->email)->queue(new PaymentReminderMail($milestone));
        }

        $communication->notify($milestone, 'payment_reminder', [
            'email' => $customer?->email,
            'whatsapp' => $customer?->whatsapp,
            'subject' => 'Payment reminder',
            'body' => 'A payment of '.$milestone->name.' is due.',
        ]);
    }
}
