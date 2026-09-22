<?php

namespace App\Livewire\Studio\Settings;

use App\Livewire\Studio\Settings\Concerns\ManagesStudioSettings;
use App\Services\Drive\GoogleDriveService;
use App\Support\StudioIntegrations;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Google Drive')]
class GoogleDrive extends Component
{
    use ManagesStudioSettings;

    public string $client_id = '';

    public string $client_secret = '';

    public string $refresh_token = '';

    public string $folder_id = '';

    public bool $client_secret_set = false;

    public bool $refresh_token_set = false;

    public function mount(): void
    {
        $this->ensureSettingsAdmin();
        $data = StudioIntegrations::formValues('google_drive', [
            'client_id' => '',
            'folder_id' => '',
        ]);
        $this->client_id = (string) ($data['client_id'] ?? '');
        $this->folder_id = (string) ($data['folder_id'] ?? '');
        $this->client_secret_set = StudioIntegrations::secretIsSet('google_drive', 'client_secret');
        $this->refresh_token_set = StudioIntegrations::secretIsSet('google_drive', 'refresh_token');
    }

    public function save(): void
    {
        $this->ensureSettingsAdmin();
        $this->validate([
            'client_id' => 'nullable|string|max:255',
            'client_secret' => 'nullable|string|max:500',
            'refresh_token' => 'nullable|string|max:2000',
            'folder_id' => 'nullable|string|max:255',
        ]);

        $org = Tenant::current();
        $merged = StudioIntegrations::merge('google_drive', [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $this->refresh_token,
            'folder_id' => $this->folder_id,
        ], $org);

        $org->settings()->update(['google_drive' => $merged]);
        $org->load('settings');
        StudioIntegrations::apply($org);

        $this->client_secret = '';
        $this->refresh_token = '';
        $this->client_secret_set = StudioIntegrations::secretIsSet('google_drive', 'client_secret');
        $this->refresh_token_set = StudioIntegrations::secretIsSet('google_drive', 'refresh_token');

        session()->flash('status', 'Google Drive settings saved.');
    }

    public function render()
    {
        return view('livewire.studio.settings.google-drive', [
            'configured' => app(GoogleDriveService::class)->isConfigured(),
        ]);
    }
}
