<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Mail\LeadConfirmationMail;
use App\Models\User;
use App\Notifications\LeadCreatedNotification;
use App\Services\CommunicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendLeadCommunications implements ShouldQueue
{
    public function __construct(protected CommunicationService $communication) {}

    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;

        if ($lead->email) {
            Mail::to($lead->email)->queue(new LeadConfirmationMail($lead));
        }

        $this->communication->notify($lead, 'lead_acknowledgement', [
            'email' => $lead->email,
            'whatsapp' => $lead->whatsapp,
            'subject' => 'We received your wedding enquiry',
            'body' => 'Thank you '.$lead->name.'. Your enquiry '.$lead->lead_number.' is with our team.',
        ]);

        if ($lead->assigned_to) {
            User::query()->find($lead->assigned_to)?->notify(new LeadCreatedNotification($lead));
        }
    }
}
