<?php

namespace App\Enums;

enum CommunicationChannel: string
{
    case Email = 'email';
    case WhatsApp = 'whatsapp';
    case InApp = 'in_app';
    case Voice = 'voice';

}
