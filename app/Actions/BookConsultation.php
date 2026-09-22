<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Events\ConsultationBooked;
use App\Models\Consultation;
use App\Models\ConsultationSlot;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookConsultation
{
    public function __construct(protected ActivityLogger $activity) {}

    public function handle(ConsultationSlot $slot, array $data, ?Lead $lead = null): Consultation
    {
        return DB::transaction(function () use ($slot, $data, $lead) {
            $slot->refresh();

            if (! $slot->is_available) {
                throw ValidationException::withMessages([
                    'slot' => 'This consultation slot is no longer available.',
                ]);
            }

            $starts = $slot->date->copy()->setTimeFromTimeString($slot->start_time);
            $ends = $slot->date->copy()->setTimeFromTimeString($slot->end_time);

            $consultation = Consultation::query()->create([
                'organization_id' => $slot->organization_id,
                'lead_id' => $lead?->id,
                'slot_id' => $slot->id,
                'staff_user_id' => $slot->staff_user_id,
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'starts_at' => $starts,
                'ends_at' => $ends,
                'timezone' => $slot->timezone,
                'meeting_link' => $data['meeting_link'] ?? null,
                'status' => 'scheduled',
                'notes' => $data['notes'] ?? null,
            ]);

            $slot->update(['is_available' => false]);

            if ($lead) {
                $lead->update(['status' => LeadStatus::Consultation]);
            }

            $this->activity->log('consultation.booked', $consultation);
            event(new ConsultationBooked($consultation));

            return $consultation;
        });
    }
}
