<?php

namespace App\Support;

class YoutubeEmbed
{
    /**
     * Extract a YouTube video ID from common watch, share, embed, and shorts URLs.
     */
    public static function videoId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $patterns = [
            '/(?:youtube\.com\/watch\?(?:.*&)?v=|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            '/^([a-zA-Z0-9_-]{11})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function embedUrl(?string $url): ?string
    {
        $id = self::videoId($url);

        return $id ? 'https://www.youtube-nocookie.com/embed/'.$id : null;
    }

    public static function isValid(?string $url): bool
    {
        if (trim((string) $url) === '') {
            return true;
        }

        return self::videoId($url) !== null;
    }
}
