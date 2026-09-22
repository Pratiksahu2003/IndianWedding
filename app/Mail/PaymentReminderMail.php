<?php

namespace App\Mail;

use App\Models\PaymentMilestone;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PaymentMilestone $milestone) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Payment reminder'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.payment-reminder');
    }
}
