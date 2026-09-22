<?php

namespace App\Jobs;

use App\Contracts\EmailProvider;
use App\Models\CommunicationLog;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendEmailMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $logId) {}

    public function handle(EmailProvider $provider): void
    {
        $log = CommunicationLog::query()->find($this->logId);
        if (! $log || $log->status === 'sent') {
            return;
        }

        $result = $provider->send($log->recipient, $log->subject ?? 'Message', $log->body ?? '');
        $log->update([
            'status' => 'sent',
            'provider_message_id' => $result['id'] ?? null,
            'sent_at' => now(),
        ]);

        EmailLog::query()->create([
            'organization_id' => $log->organization_id,
            'to_email' => $log->recipient,
            'subject' => $log->subject,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
