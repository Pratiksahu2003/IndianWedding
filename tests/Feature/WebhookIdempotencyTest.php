<?php

namespace Tests\Feature;

use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_payment_webhooks_are_ignored(): void
    {
        $payload = ['id' => 'evt_123', 'type' => 'payment.paid', 'data' => ['object' => ['status' => 'paid']]];

        $this->postJson('/webhooks/payments/manual', $payload, ['X-Signature' => 'test'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->postJson('/webhooks/payments/manual', $payload, ['X-Signature' => 'test'])
            ->assertOk()
            ->assertJson(['duplicate' => true]);

        $this->assertSame(1, WebhookEvent::query()->count());
    }
}
