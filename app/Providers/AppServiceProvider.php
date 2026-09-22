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
use App\Services\Communication\SmtpEmailProvider;
use App\Services\StorageService;
use App\Support\StudioIntegrations;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmailProvider::class, function () {
            return config('mail.default') === 'smtp'
                ? $this->app->make(SmtpEmailProvider::class)
                : $this->app->make(LogEmailProvider::class);
        });
        $this->app->singleton(CloudStorage::class, StorageService::class);
        $this->app->singleton(AiProvider::class, NullAiProvider::class);
        $this->app->bind(WhatsAppProvider::class, function () {
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

        \Illuminate\Support\Facades\View::composer('layouts.public', function ($view) {
            if (! $view->offsetExists('organization')) {
                $view->with('organization', \App\Support\Tenant::current()
                    ?? \App\Models\Organization::query()->where('is_active', true)->first());
            }
        });
    }
}
