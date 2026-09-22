<?php

namespace App\Services\Communication;

use App\Contracts\EmailProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SmtpEmailProvider implements EmailProvider
{
    public function send(string $to, string $subject, string $html, array $meta = []): array
    {
        $id = (string) Str::uuid();

        Mail::html($html, function ($message) use ($to, $subject): void {
            $message->to($to)->subject($subject);
        });

        return ['id' => $id, 'status' => 'sent', 'provider' => 'smtp'];
    }
}
