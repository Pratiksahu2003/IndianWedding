<?php

namespace App\Enums;

enum EventType: string
{
    case Engagement = 'engagement';
    case Mehndi = 'mehndi';
    case Haldi = 'haldi';
    case Wedding = 'wedding';
    case Reception = 'reception';
    case PreWedding = 'pre_wedding';
    case Other = 'other';

}
