<?php

namespace App\Livewire\Studio\Settings;

use App\Livewire\Studio\Settings\Concerns\ManagesStudioSettings;
use App\Support\StudioIntegrations;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Email / SMTP')]
class EmailSmtp extends Component
{
    use ManagesStudioSettings;

    public string $mailer = 'log';

    public string $host = '';

    public string $port = '587';

    public string $username = '';

    public string $password = '';

    public string $encryption = 'tls';

    public string $from_address = '';

    public string $from_name = '';

    public bool $password_set = false;

    public function mount(): void
    {
        $this->ensureSettingsAdmin();
        $org = Tenant::current();
        $data = StudioIntegrations::formValues('email', [
            'mailer' => 'log',
            'host' => '',
            'port' => '587',
            'username' => '',
            'encryption' => 'tls',
            'from_address' => (string) $org?->email,
            'from_name' => (string) $org?->name,
        ]);
        $this->mailer = (string) ($data['mailer'] ?? 'log');
        $this->host = (string) ($data['host'] ?? '');
        $this->port = (string) ($data['port'] ?? '587');
        $this->username = (string) ($data['username'] ?? '');
        $this->encryption = (string) ($data['encryption'] ?? 'tls');
        $this->from_address = (string) ($data['from_address'] ?? '');
        $this->from_name = (string) ($data['from_name'] ?? '');
        $this->password_set = StudioIntegrations::secretIsSet('email', 'password');
    }

    public function save(): void
    {
        $this->ensureSettingsAdmin();
        $this->validate([
            'mailer' => 'required|in:log,smtp',
            'host' => 'nullable|required_if:mailer,smtp|string|max:255',
            'port' => 'nullable|required_if:mailer,smtp|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:500',
            'encryption' => 'nullable|in:tls,ssl,none',
            'from_address' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
        ]);

        $org = Tenant::current();
        $merged = StudioIntegrations::merge('email', [
            'mailer' => $this->mailer,
            'host' => $this->host,
            'port' => (int) $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
            'from_address' => $this->from_address,
            'from_name' => $this->from_name,
        ], $org);

        $org->settings()->update(['email' => $merged]);
        $org->load('settings');
        StudioIntegrations::apply($org);

        $this->password = '';
        $this->password_set = StudioIntegrations::secretIsSet('email', 'password');

        session()->flash('status', 'Email / SMTP settings saved.');
    }

    public function render()
    {
        return view('livewire.studio.settings.email-smtp');
    }
}
