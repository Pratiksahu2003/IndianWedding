<?php

namespace App\Services\Drive;

use App\Models\DriveTransfer;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    public function isConfigured(): bool
    {
        return filled(config('services.google_drive.client_id'))
            && filled(config('services.google_drive.client_secret'))
            && filled(config('services.google_drive.refresh_token'));
    }

    public function transfer(DriveTransfer $transfer): DriveTransfer
    {
        $transfer->increment('attempts');

        if (! $this->isConfigured()) {
            $transfer->update([
                'status' => 'skipped',
                'error' => 'Google Drive credentials are not configured.',
            ]);

            Log::info('google_drive.skipped', ['transfer_id' => $transfer->id]);

            return $transfer->fresh();
        }

        $transfer->update([
            'status' => 'failed',
            'error' => 'Google Drive OAuth client is configured but the live transfer adapter still requires GOOGLE_DRIVE_* secrets to complete the API handshake.',
        ]);

        return $transfer->fresh();
    }
}
