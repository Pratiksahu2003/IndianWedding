<?php

namespace App\Support;

class PackageOptions
{
    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            'service' => 'Service (photography / cinematography)',
            'wedding' => 'Wedding package (Silver, Gold, etc.)',
            'production' => 'Production package (music video, films, etc.)',
            'addon' => 'Wedding add-on',
            'candid' => 'Candid / documentary shoot',
            'corporate' => 'Corporate & brand films',
            'event' => 'Birthday & event coverage',
            'music_video' => 'Music video production',
            'product_shoot' => 'Product photography / videography',
            'destination' => 'Destination wedding / travel coverage',
        ];
    }

    /** @return array<string, string> */
    public static function includes(): array
    {
        return [
            'includes_album' => 'Album / photobook',
            'includes_video' => 'Cinematic video / film',
            'includes_pre_wedding' => 'Pre-wedding shoot',
            'includes_drone' => 'Drone photography & video',
            'includes_candid' => 'Candid photography',
            'includes_instagram_reels' => 'Instagram / reels videos',
            'includes_same_day_edit' => 'Same-day edit / highlight',
            'includes_live_streaming' => 'Live wedding streaming',
            'includes_invitation_video' => 'Wedding invitation video',
            'includes_destination' => 'Destination / multi-location',
        ];
    }

    /** @return list<string> */
    public static function typeKeys(): array
    {
        return array_keys(self::types());
    }

    /** @return list<string> */
    public static function includeKeys(): array
    {
        return array_keys(self::includes());
    }

    public static function typeLabel(string $type): string
    {
        return self::types()[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /** @return list<string> */
    public static function typesForPublicPage(string $page): array
    {
        return match ($page) {
            'services' => ['service', 'candid', 'event'],
            'packages' => ['wedding'],
            'production' => ['production', 'corporate', 'music_video', 'product_shoot', 'destination'],
            default => [],
        };
    }

    public static function listingPageForType(string $type): string
    {
        return match ($type) {
            'wedding', 'addon' => 'packages',
            'production', 'corporate', 'music_video', 'product_shoot', 'destination' => 'production',
            default => 'services',
        };
    }
}
