<?php

namespace App\Contracts;

interface EmailProvider
{
    public function send(string $to, string $subject, string $html, array $meta = []): array;
}
