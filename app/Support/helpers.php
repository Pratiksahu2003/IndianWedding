<?php

use App\Support\SiteCopy;

if (! function_exists('site')) {
    function site(string $key, ?string $default = null): string
    {
        return SiteCopy::get($key, $default);
    }
}

if (! function_exists('studio_logo')) {
    function studio_logo(): string
    {
        return asset('Logo/logo.png');
    }
}

if (! function_exists('studio_logo_src')) {
    function studio_logo_src(): string
    {
        $web = public_path('Logo/logo-web.png');

        return file_exists($web) ? asset('Logo/logo-web.png') : studio_logo();
    }
}

if (! function_exists('contact_emails')) {
    /**
     * @return list<string>
     */
    function contact_emails(): array
    {
        $raw = trim(site('contact.emails', ''));
        if ($raw === '') {
            return [];
        }

        $emails = [];
        foreach (preg_split('/\R+/', $raw) as $line) {
            foreach (preg_split('/[\s,;]+/', trim($line), -1, PREG_SPLIT_NO_EMPTY) as $part) {
                $part = trim($part);
                if ($part !== '' && filter_var($part, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $part;
                }
            }
        }

        return array_values(array_unique($emails));
    }
}

if (! function_exists('rich_html')) {
    /**
     * Sanitize HTML from the studio rich text editor for safe output.
     */
    function rich_html(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><blockquote>';

        return trim(strip_tags($html, $allowed));
    }
}

if (! function_exists('site_rich')) {
    function site_rich(string $key, ?string $default = null): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(rich_html(site($key, $default)));
    }
}

if (! function_exists('social_links')) {
    /**
     * @return list<array{id: string, label: string, url: string}>
     */
    function social_links(): array
    {
        return \App\Support\SocialLinks::all();
    }
}
