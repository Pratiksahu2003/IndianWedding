<?php

namespace App\Services\Communication;

use App\Contracts\WhatsAppProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogWhatsAppProvider implements WhatsAppProvider
{
    public function send(string $to, string $template, array $variables = [], ?string $message = null): array
    {
        if (! config('services.whatsapp.enabled')) {
            Log::info('whatsapp.skipped', ['reason' => 'not_configured', 'to' => $to, 'template' => $template]);

            return ['id' => null, 'status' => 'skipped', 'provider' => 'log'];
        }

        $id = (string) Str::uuid();
        Log::info('whatsapp.queued', compact('to', 'template', 'variables', 'message') + ['id' => $id]);

        return ['id' => $id, 'status' => 'logged', 'provider' => 'log'];
    }
}
