<?php

namespace App\Contracts;

interface WhatsAppProvider
{
    public function send(string $to, string $template, array $variables = [], ?string $message = null): array;
}
