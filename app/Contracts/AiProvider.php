<?php

namespace App\Contracts;

interface AiProvider
{
    public function scoreLead(array $payload): array;

    public function suggestReply(string $context): string;
}
