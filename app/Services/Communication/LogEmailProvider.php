<?php

namespace App\Services\Communication;

use App\Contracts\EmailProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogEmailProvider implements EmailProvider
{
    public function send(string $to, string $subject, string $html, array $meta = []): array
    {
        $id = (string) Str::uuid();
        Log::info('email.queued', ['to' => $to, 'subject' => $subject, 'id' => $id, 'meta' => $meta]);

        return ['id' => $id, 'status' => 'logged', 'provider' => 'log'];
    }
}
