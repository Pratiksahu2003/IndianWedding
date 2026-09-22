<?php

namespace App\Http\Controllers\Webhook;

use App\Actions\RecordPayment;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Services\AuditLogger;
use App\Services\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, string $provider, PaymentGatewayManager $gateways, RecordPayment $record, AuditLogger $audit): JsonResponse
    {
        $raw = $request->getContent();
        $gateway = $gateways->driver($provider);

        if (! $gateway->verifyWebhook($raw, $request->header('X-Signature') ?? $request->header('Stripe-Signature'))) {
            Log::warning('webhook.invalid_signature', ['provider' => $provider]);

            return response()->json(['ok' => false], 401);
        }

        $payload = $request->all();
        $parsed = $gateway->parseWebhook($payload);
        $eventId = (string) ($parsed['event_id'] ?? sha1($raw));

        $existing = WebhookEvent::query()->where('provider', $provider)->where('event_id', $eventId)->first();
        if ($existing) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        $event = WebhookEvent::query()->create([
            'provider' => $provider,
            'event_id' => $eventId,
            'event_type' => $parsed['type'] ?? null,
            'status' => 'received',
            'payload' => $payload,
            'signature' => $request->header('X-Signature'),
            'ip_address' => $request->ip(),
        ]);

        $payment = isset($parsed['payment_id'])
            ? Payment::withoutTenant()->where('ulid', $parsed['payment_id'])->orWhere('reference', $parsed['payment_id'])->first()
            : null;

        if ($payment && ($parsed['status'] ?? null) === 'paid' && $payment->milestone) {
            $record->handle($payment->milestone, $payment->amount, [
                'gateway' => $provider,
                'provider_payment_id' => $parsed['provider_payment_id'] ?? null,
                'payload' => $payload,
            ]);
        }

        $event->update(['status' => 'processed', 'processed_at' => now()]);
        $audit->log('webhook.processed', $event, [], ['provider' => $provider]);

        return response()->json(['ok' => true]);
    }
}
