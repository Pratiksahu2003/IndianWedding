<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Models\WhatsappMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): mixed
    {
        $token = config('services.whatsapp.verify_token');
        if ($request->query('hub_mode') === 'subscribe' && hash_equals((string) $token, (string) $request->query('hub_verify_token'))) {
            return response($request->query('hub_challenge'), 200);
        }

        return response('forbidden', 403);
    }

    public function store(Request $request): JsonResponse
    {
        $eventId = (string) data_get($request->all(), 'entry.0.id', sha1($request->getContent()));
        WebhookEvent::query()->firstOrCreate(
            ['provider' => 'whatsapp', 'event_id' => $eventId],
            ['event_type' => 'status', 'payload' => $request->all(), 'status' => 'processed', 'processed_at' => now(), 'ip_address' => $request->ip()],
        );

        $status = data_get($request->all(), 'entry.0.changes.0.value.statuses.0.status');
        $providerId = data_get($request->all(), 'entry.0.changes.0.value.statuses.0.id');
        if ($providerId) {
            WhatsappMessage::withoutTenant()->where('provider_message_id', $providerId)->update([
                'status' => $status ?: 'updated',
                'delivered_at' => $status === 'delivered' ? now() : null,
                'read_at' => $status === 'read' ? now() : null,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
