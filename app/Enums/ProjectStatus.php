<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case BookingConfirmed = 'booking_confirmed';
    case TeamAssigned = 'team_assigned';
    case WeddingShoot = 'wedding_shoot';
    case RawFilesUploaded = 'raw_files_uploaded';
    case EditorAssigned = 'editor_assigned';
    case Editing = 'editing';
    case InternalReview = 'internal_review';
    case ClientPreview = 'client_preview';
    case FinalPayment = 'final_payment';
    case FinalDelivery = 'final_delivery';
    case Completed = 'completed';

    public function label(): string
    {
        return str_replace('_', ' ', ucwords($this->value, '_'));
    }

}
