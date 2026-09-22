<?php

namespace App\Support;

use App\Models\Organization;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class StudioIntegrations
{
    /** @var array<string, mixed> */
    private static array $defaults = [];

    public const SECRET_KEYS = [
        'payments' => ['stripe_secret', 'stripe_webhook_secret', 'razorpay_secret', 'razorpay_webhook_secret'],
        'email' => ['password'],
        'whatsapp' => ['token', 'verify_token'],
        'google_drive' => ['client_secret', 'refresh_token'],
        'storage' => ['gcs_key_file'],
    ];

    public static function apply(?Organization $org = null): void
    {
        self::captureDefaults();
        config(self::$defaults);

        $org ??= Tenant::current();
        if (! $org?->settings) {
            return;
        }

        $mail = self::bag('email', $org);
        if (($mail['mailer'] ?? 'log') === 'smtp' && filled($mail['host'] ?? null)) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $mail['host'],
                'mail.mailers.smtp.port' => (int) ($mail['port'] ?? 587),
                'mail.mailers.smtp.username' => $mail['username'] ?? null,
                'mail.mailers.smtp.password' => $mail['password'] ?? null,
                'mail.mailers.smtp.encryption' => ($mail['encryption'] ?? 'tls') === 'none' ? null : ($mail['encryption'] ?? 'tls'),
                'mail.from.address' => $mail['from_address'] ?: config('mail.from.address'),
                'mail.from.name' => $mail['from_name'] ?: config('mail.from.name'),
            ]);
        }

        $payments = self::bag('payments', $org);
        if (filled($payments['default'] ?? null)) {
            config(['payments.default' => $payments['default']]);
        }
        config([
            'services.stripe.key' => $payments['stripe_key'] ?? config('services.stripe.key'),
            'services.stripe.secret' => $payments['stripe_secret'] ?? config('services.stripe.secret'),
            'services.stripe.webhook_secret' => $payments['stripe_webhook_secret'] ?? config('services.stripe.webhook_secret'),
            'services.razorpay.key' => $payments['razorpay_key'] ?? config('services.razorpay.key'),
            'services.razorpay.secret' => $payments['razorpay_secret'] ?? config('services.razorpay.secret'),
            'services.razorpay.webhook_secret' => $payments['razorpay_webhook_secret'] ?? config('services.razorpay.webhook_secret'),
        ]);

        $whatsapp = self::bag('whatsapp', $org);
        config([
            'services.whatsapp.enabled' => (bool) ($whatsapp['enabled'] ?? config('services.whatsapp.enabled')),
            'services.whatsapp.provider' => $whatsapp['provider'] ?? config('services.whatsapp.provider'),
            'services.whatsapp.token' => $whatsapp['token'] ?? config('services.whatsapp.token'),
            'services.whatsapp.from' => $whatsapp['from'] ?? config('services.whatsapp.from'),
            'services.whatsapp.verify_token' => $whatsapp['verify_token'] ?? config('services.whatsapp.verify_token'),
        ]);

        $drive = self::bag('google_drive', $org);
        config([
            'services.google_drive.client_id' => $drive['client_id'] ?? config('services.google_drive.client_id'),
            'services.google_drive.client_secret' => $drive['client_secret'] ?? config('services.google_drive.client_secret'),
            'services.google_drive.refresh_token' => $drive['refresh_token'] ?? config('services.google_drive.refresh_token'),
        ]);

        $storage = self::bag('storage', $org);
        config([
            'services.gcs.project' => $storage['gcs_project'] ?? config('services.gcs.project'),
            'services.gcs.bucket' => $storage['gcs_bucket'] ?? config('services.gcs.bucket'),
            'services.gcs.key_file' => $storage['gcs_key_file'] ?? config('services.gcs.key_file'),
        ]);
    }

    public static function applyFirstStudio(): void
    {
        self::apply(Organization::query()->where('is_active', true)->first());
    }

    public static function bag(string $name, ?Organization $org = null): array
    {
        $org ??= Tenant::current();
        $raw = $org?->settings?->{$name} ?? [];

        return self::decryptBag(is_array($raw) ? $raw : []);
    }

    public static function get(string $dot, mixed $default = null, ?Organization $org = null): mixed
    {
        [$bag, $key] = array_pad(explode('.', $dot, 2), 2, null);
        if (! $bag || ! $key) {
            return $default;
        }

        $value = data_get(self::bag($bag, $org), $key);

        return filled($value) || $value === false || $value === 0 ? $value : $default;
    }

    public static function secretIsSet(string $bag, string $key, ?Organization $org = null): bool
    {
        $org ??= Tenant::current();
        $raw = $org?->settings?->{$bag}[$key] ?? null;

        return filled($raw);
    }

    public static function merge(string $bag, array $incoming, ?Organization $org = null): array
    {
        $org ??= Tenant::current();
        $existing = is_array($org?->settings?->{$bag}) ? $org->settings->{$bag} : [];
        $secrets = self::SECRET_KEYS[$bag] ?? [];

        foreach ($secrets as $key) {
            $new = $incoming[$key] ?? '';
            $incoming[$key] = filled($new)
                ? 'enc:'.Crypt::encryptString((string) $new)
                : ($existing[$key] ?? null);
        }

        return array_merge($existing, $incoming);
    }

    public static function formValues(string $bag, array $defaults = [], ?Organization $org = null): array
    {
        $data = array_merge($defaults, self::bag($bag, $org));
        foreach (self::SECRET_KEYS[$bag] ?? [] as $key) {
            $data[$key] = '';
        }

        return $data;
    }

    private static function decryptBag(array $bag): array
    {
        foreach ($bag as $key => $value) {
            if (! is_string($value) || ! str_starts_with($value, 'enc:')) {
                continue;
            }
            try {
                $bag[$key] = Crypt::decryptString(substr($value, 4));
            } catch (Throwable) {
                $bag[$key] = null;
            }
        }

        return $bag;
    }

    private static function captureDefaults(): void
    {
        if (self::$defaults !== []) {
            return;
        }

        self::$defaults = [
            'mail.default' => config('mail.default'),
            'mail.mailers.smtp.host' => config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => config('mail.mailers.smtp.password'),
            'mail.mailers.smtp.encryption' => config('mail.mailers.smtp.encryption'),
            'mail.from.address' => config('mail.from.address'),
            'mail.from.name' => config('mail.from.name'),
            'payments.default' => config('payments.default'),
            'services.stripe.key' => config('services.stripe.key'),
            'services.stripe.secret' => config('services.stripe.secret'),
            'services.stripe.webhook_secret' => config('services.stripe.webhook_secret'),
            'services.razorpay.key' => config('services.razorpay.key'),
            'services.razorpay.secret' => config('services.razorpay.secret'),
            'services.razorpay.webhook_secret' => config('services.razorpay.webhook_secret'),
            'services.whatsapp.enabled' => config('services.whatsapp.enabled'),
            'services.whatsapp.provider' => config('services.whatsapp.provider'),
            'services.whatsapp.token' => config('services.whatsapp.token'),
            'services.whatsapp.from' => config('services.whatsapp.from'),
            'services.whatsapp.verify_token' => config('services.whatsapp.verify_token'),
            'services.google_drive.client_id' => config('services.google_drive.client_id'),
            'services.google_drive.client_secret' => config('services.google_drive.client_secret'),
            'services.google_drive.refresh_token' => config('services.google_drive.refresh_token'),
            'services.gcs.project' => config('services.gcs.project'),
            'services.gcs.bucket' => config('services.gcs.bucket'),
            'services.gcs.key_file' => config('services.gcs.key_file'),
        ];
    }
}
