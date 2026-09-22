<?php

namespace App\Jobs;

use App\Models\DriveTransfer;
use App\Services\Drive\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TransferToGoogleDrive implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $transferId) {}

    public function handle(GoogleDriveService $drive): void
    {
        $transfer = DriveTransfer::withoutTenant()->find($this->transferId);
        if (! $transfer || $transfer->status === 'completed') {
            return;
        }

        $drive->transfer($transfer);
    }
}
