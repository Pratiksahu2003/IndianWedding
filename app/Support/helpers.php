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
