<?php

namespace App\Support;

class SocialLinks
{
    /**
     * @return list<array{id: string, label: string, url: string}>
     */
    public static function all(): array
    {
        return SocialPlatforms::configuredLinks();
    }
}
