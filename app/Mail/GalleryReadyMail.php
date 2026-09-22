<?php

namespace App\Mail;

use App\Models\Gallery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GalleryReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Gallery $gallery) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Your final gallery is ready'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.gallery-ready');
    }
}
