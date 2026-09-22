<?php

namespace App\Services;

use App\Contracts\EmailProvider;
use App\Contracts\WhatsAppProvider;
use App\Enums\CommunicationChannel;
use App\Jobs\SendEmailMessage;
use App\Jobs\SendWhatsAppMessage;
use App\Models\CommunicationLog;
use App\Models\WhatsappMessage;
use Illuminate\Database\Eloquent\Model;

class CommunicationService
{
    public function __construct(
        protected EmailProvider $email,
        protected WhatsAppProvider $whatsapp,
        protected ActivityLogger $activity,
    ) {}

    public function notify(Model $entity, string $template, array $payload): void
    {
        $email = $payload['email'] ?? null;
        $phone = $payload['whatsapp'] ?? $payload['phone'] ?? null;
        $subject = $payload['subject'] ?? $template;
        $body = $payload['body'] ?? '';

        if ($email) {
            $log = CommunicationLog::query()->create([
                'organization_id' => $entity->organization_id ?? null,
                'communicable_type' => $entity::class,
                'communicable_id' => $entity->getKey(),
                'channel' => CommunicationChannel::Email,
                'template' => $template,
                'recipient' => $email,
                'subject' => $subject,
                'body' => $body,
                'status' => 'queued',
                'provider' => 'mail',
            ]);

            SendEmailMessage::dispatch($log->id);
        }

        if ($phone) {
            $message = WhatsappMessage::query()->create([
                'organization_id' => $entity->organization_id ?? null,
                'template_key' => $template,
                'recipient' => $phone,
                'message' => $body,
                'provider' => config('services.whatsapp.provider', 'log'),
                'status' => 'queued',
            ]);

            SendWhatsAppMessage::dispatch($message->id);
        }
    }
}
