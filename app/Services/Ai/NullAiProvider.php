<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;

class NullAiProvider implements AiProvider
{
    public function scoreLead(array $payload): array
    {
        return ['score' => null, 'provider' => 'none', 'reason' => 'AI is not configured.'];
    }

    public function suggestReply(string $context): string
    {
        return '';
    }
}
