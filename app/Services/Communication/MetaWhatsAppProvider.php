<?php

namespace App\Services\Communication;

use App\Contracts\WhatsAppProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaWhatsAppProvider implements WhatsAppProvider
{
    public function send(string $to, string $template, array $variables = [], ?string $message = null): array
    {
        $token = config('services.whatsapp.token');
        $from = config('services.whatsapp.from');

        if (! $token || ! $from) {
            throw new RuntimeException('WhatsApp credentials are not configured.');
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => 'en'],
                'components' => [[
                    'type' => 'body',
                    'parameters' => array_map(fn ($value) => ['type' => 'text', 'text' => (string) $value], array_values($variables)),
                ]],
            ],
        ];

        $response = Http::withToken($token)
            ->post('https://graph.facebook.com/v20.0/'.$from.'/messages', $payload);

        if ($response->failed()) {
            throw new RuntimeException('WhatsApp provider error: '.$response->body());
        }

        return [
            'id' => $response->json('messages.0.id'),
            'status' => 'queued',
            'provider' => 'meta',
        ];
    }
}
