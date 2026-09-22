<?php

namespace App\Providers;

use App\Contracts\AiProvider;
use App\Contracts\CloudStorage;
use App\Contracts\EmailProvider;
use App\Contracts\WhatsAppProvider;
use App\Services\Ai\NullAiProvider;
use App\Services\Communication\LogEmailProvider;
use App\Services\Communication\LogWhatsAppProvider;
use App\Services\Communication\MetaWhatsAppProvider;
use App\Services\StorageService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailProvider::class, LogEmailProvider::class);
        $this->app->singleton(CloudStorage::class, StorageService::class);
        $this->app->singleton(AiProvider::class, NullAiProvider::class);
        $this->app->singleton(WhatsAppProvider::class, function () {
            return config('services.whatsapp.provider') === 'meta'
                ? $this->app->make(MetaWhatsAppProvider::class)
                : $this->app->make(LogWhatsAppProvider::class);
        });
    }

    public function boot(): void
    {
        RateLimiter::for('inquiry', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
