<?php

namespace App\Support;

/**
 * Media sourced from https://unikstudio.in/ (Unik Studio WordPress uploads).
 */
class UnikStudioAssets
{
    public const BASE = 'https://unikstudio.in/wp-content/uploads';

    /** @return array<string, string> local filename => remote URL */
    public static function downloads(): array
    {
        return [
            'hero-slide.png' => self::BASE.'/2025/09/slide-2.png',
            'hero-header.png' => self::BASE.'/2025/08/Hader-imag-1.png',
            'about.png' => self::BASE.'/2025/09/about_1-1-1-1.png',
            'banner-home.png' => self::BASE.'/2025/09/home-3.png',
            'auth-couple.jpg' => self::BASE.'/2026/01/6Y0A2094-copy-scaled.jpg',
            'auth-detail.jpg' => self::BASE.'/2026/01/6Y0A2111-copy-2-scaled.jpg',
            'portfolio-01.jpg' => self::BASE.'/2026/01/6Y0A2094-copy-scaled.jpg',
            'portfolio-02.jpg' => self::BASE.'/2026/01/6Y0A2111-copy-2-scaled.jpg',
            'portfolio-03.jpg' => self::BASE.'/2026/01/6Y0A2205-copy-scaled.jpg',
            'portfolio-04.jpg' => self::BASE.'/2026/01/6Y0A2692-ocopy-scaled.jpg',
            'portfolio-05.jpg' => self::BASE.'/2023/08/6Y0A9446-scaled.jpg',
            'portfolio-06.jpg' => self::BASE.'/2023/08/6Y0A9054-scaled.jpg',
            'portfolio-07.jpg' => self::BASE.'/2026/01/6Y0A2216-copy-scaled.jpg',
            'portfolio-08.jpg' => self::BASE.'/2026/01/6Y0A2230-copy-scaled.jpg',
            'portfolio-09.jpg' => self::BASE.'/2023/08/6Y0A9137-scaled.jpg',
            'portfolio-10.jpg' => self::BASE.'/2023/08/6Y0A9374-scaled.jpg',
            'portfolio-11.jpg' => self::BASE.'/2026/01/6Y0A2708-copy-scaled.jpg',
            'portfolio-12.jpg' => self::BASE.'/2026/01/6Y0A2776-copy-scaled.jpg',
            'portfolio-13.jpg' => self::BASE.'/2023/08/6Y0A8072-scaled.jpg',
            'portfolio-14.jpg' => self::BASE.'/2023/08/6Y0A9031-scaled.jpg',
            'portfolio-15.jpg' => self::BASE.'/2023/08/6Y0A9430-scaled.jpg',
            'portfolio-16.jpg' => self::BASE.'/2026/01/i6Y0A2080-copy-scaled.jpg',
            'portfolio-17.jpg' => self::BASE.'/2026/01/i6Y0A2083-copy-scaled.jpg',
            'portfolio-18.jpg' => self::BASE.'/2026/01/6Y0A21699-copy-scaled.jpg',
            'portfolio-19.jpg' => self::BASE.'/2026/01/6Y0A21996-copy-scaled.jpg',
            'portfolio-20.jpg' => self::BASE.'/2026/01/6Y00A2230-copy-scaled.jpg',
            'service-wedding.jpg' => self::BASE.'/2023/08/6Y0A9446-scaled.jpg',
            'service-cinema.jpg' => self::BASE.'/2026/01/6Y0A2205-copy-scaled.jpg',
            'service-candid.jpg' => self::BASE.'/2023/08/6Y0A9054-scaled.jpg',
            'service-prewedding.jpg' => self::BASE.'/2026/01/6Y0A2094-copy-scaled.jpg',
            'service-birthday.jpg' => self::BASE.'/2026/01/6Y0A2708-copy-scaled.jpg',
            'service-music.jpg' => self::BASE.'/2026/01/6Y0A2776-copy-scaled.jpg',
        ];
    }

    public static function publicPath(string $filename): string
    {
        return '/images/unik/'.$filename;
    }

    public static function url(string $filename): string
    {
        $path = public_path('images/unik/'.$filename);

        if (is_file($path)) {
            return asset(self::publicPath($filename));
        }

        return self::downloads()[$filename] ?? asset(self::publicPath($filename));
    }

    /**
     * @return list<array{title: string, file: string, category: string, couple: string, story: string}>
     */
    public static function portfolioCatalog(): array
    {
        return [
            ['title' => 'Pre-Wedding', 'file' => 'portfolio-01.jpg', 'category' => 'pre-wedding', 'couple' => 'Anjali & Abhay', 'story' => 'A golden-hour pre-wedding in Delhi — the same warmth you see on unikstudio.in.'],
            ['title' => 'Beautiful Day — Vivaah', 'file' => 'portfolio-02.jpg', 'category' => 'wedding', 'couple' => 'Vivaah', 'story' => 'Cinematic wedding frames from a beautiful day celebration.'],
            ['title' => 'Holding Hands', 'file' => 'portfolio-03.jpg', 'category' => 'candid', 'couple' => 'Couple', 'story' => 'Forever and always — candid love photographed without posing.'],
            ['title' => 'Anjel~i', 'file' => 'portfolio-04.jpg', 'category' => 'wedding', 'couple' => 'Bridal portrait', 'story' => 'Glowing with happiness — bridal portraiture with soft light.'],
            ['title' => 'New Romantic Love Story', 'file' => 'portfolio-05.jpg', 'category' => 'pre-wedding', 'couple' => 'Love story', 'story' => 'A romantic chapter before the wedding day.'],
            ['title' => 'Wedding rituals', 'file' => 'portfolio-06.jpg', 'category' => 'wedding', 'couple' => 'Ceremony', 'story' => 'Sacred moments and family emotions during the wedding.'],
            ['title' => 'Candid joy', 'file' => 'portfolio-07.jpg', 'category' => 'candid', 'couple' => 'Celebration', 'story' => 'Laughter and tears between rituals — real wedding energy.'],
            ['title' => 'Pre-wedding outdoors', 'file' => 'portfolio-08.jpg', 'category' => 'pre-wedding', 'couple' => 'Outdoors', 'story' => 'Open light and quiet streets for your love story.'],
            ['title' => 'Reception light', 'file' => 'portfolio-09.jpg', 'category' => 'wedding', 'couple' => 'Reception', 'story' => 'Evening celebrations captured with cinematic colour.'],
            ['title' => 'Couple portrait', 'file' => 'portfolio-10.jpg', 'category' => 'pre-wedding', 'couple' => 'Portrait', 'story' => 'Editorial couple frames for albums and walls.'],
            ['title' => 'Wedding details', 'file' => 'portfolio-11.jpg', 'category' => 'wedding', 'couple' => 'Details', 'story' => 'Florals, attire and moments between the big shots.'],
            ['title' => 'Cinematic still', 'file' => 'portfolio-12.jpg', 'category' => 'cinematography', 'couple' => 'Film still', 'story' => 'Movie-language frames from Unik cinematography.'],
            ['title' => 'Family moments', 'file' => 'portfolio-13.jpg', 'category' => 'wedding', 'couple' => 'Family', 'story' => 'Generations together on the wedding day.'],
            ['title' => 'Baraat energy', 'file' => 'portfolio-14.jpg', 'category' => 'wedding', 'couple' => 'Baraat', 'story' => 'Music, movement and colour during the baraat.'],
            ['title' => 'Mandap', 'file' => 'portfolio-15.jpg', 'category' => 'wedding', 'couple' => 'Mandap', 'story' => 'Rituals photographed with patience and two-camera coverage.'],
            ['title' => 'Pre-wedding film', 'file' => 'portfolio-16.jpg', 'category' => 'pre-wedding', 'couple' => 'Film', 'story' => 'Stills that match your pre-wedding cinema.'],
            ['title' => 'Golden hour', 'file' => 'portfolio-17.jpg', 'category' => 'pre-wedding', 'couple' => 'Sunset', 'story' => 'Last light before the vows.'],
            ['title' => 'Wedding dance', 'file' => 'portfolio-18.jpg', 'category' => 'wedding', 'couple' => 'Reception', 'story' => 'Dance floor memories that keep the night alive.'],
            ['title' => 'Candid smile', 'file' => 'portfolio-19.jpg', 'category' => 'candid', 'couple' => 'Smiles', 'story' => 'Unposed smiles between ceremonies.'],
            ['title' => 'Love in motion', 'file' => 'portfolio-20.jpg', 'category' => 'pre-wedding', 'couple' => 'Motion', 'story' => 'Movement, fabric and light — pre-wedding at its best.'],
        ];
    }

    /**
     * @return array<string, string> package slug => image filename
     */
    public static function serviceCovers(): array
    {
        return [
            'wedding-photography' => 'service-wedding.jpg',
            'cinematography' => 'service-cinema.jpg',
            'candid-shoot' => 'service-candid.jpg',
            'pre-wedding-shoot' => 'service-prewedding.jpg',
            'birthday-shoot' => 'service-birthday.jpg',
            'music-video-shoot' => 'service-music.jpg',
        ];
    }

    /**
     * Stable placeholder cover for cards without an upload — each seed maps to a different image.
     */
    public static function placeholderCover(int|string $seed, ?string $context = null): string
    {
        $pool = self::placeholderPool($context);
        $index = abs(crc32((string) $seed)) % count($pool);

        return self::url($pool[$index]);
    }

    /**
     * @return list<string>
     */
    public static function placeholderPool(?string $context = null): array
    {
        $portfolio = array_map(
            fn (int $n) => sprintf('portfolio-%02d.jpg', $n),
            range(1, 20),
        );

        $services = array_values(self::serviceCovers());

        $pool = match ($context) {
            'production' => array_merge(['service-cinema.jpg', 'service-music.jpg'], $portfolio),
            'wedding' => array_merge(['service-wedding.jpg', 'service-prewedding.jpg', 'service-candid.jpg'], $portfolio),
            default => array_values(array_unique(array_merge($services, $portfolio))),
        };

        return $pool;
    }
}
