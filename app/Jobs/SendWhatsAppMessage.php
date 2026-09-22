<?php

namespace App\Jobs;

use App\Contracts\WhatsAppProvider;
use App\Models\WhatsappMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $messageId) {}

    public function handle(WhatsAppProvider $provider): void
    {
        $message = WhatsappMessage::query()->find($this->messageId);
        if (! $message || in_array($message->status, ['sent', 'skipped'], true)) {
            return;
        }

        $result = $provider->send($message->recipient, $message->template_key, [], $message->message);
        $message->update([
            'status' => $result['status'] === 'skipped' ? 'skipped' : 'sent',
            'provider_message_id' => $result['id'] ?? null,
            'provider' => $result['provider'] ?? $message->provider,
            'sent_at' => now(),
        ]);
    }
}
