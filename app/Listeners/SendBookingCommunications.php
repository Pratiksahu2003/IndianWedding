<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmationMail;
use App\Services\CommunicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingCommunications implements ShouldQueue
{
    public function __construct(protected CommunicationService $communication) {}

    public function handle(BookingConfirmed $event): void
    {
        $project = $event->project->load('customer');

        if ($project->customer?->email) {
            Mail::to($project->customer->email)->queue(new BookingConfirmationMail($project));
        }

        $this->communication->notify($project, 'booking_confirmation', [
            'email' => $project->customer?->email,
            'whatsapp' => $project->customer?->whatsapp,
            'subject' => 'Your wedding is booked',
            'body' => 'Booking confirmed for '.$project->title,
        ]);
    }
}
