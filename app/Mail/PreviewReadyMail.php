<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreviewReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Gallery $gallery) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Your preview gallery is ready'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.preview-ready');
    }
}
