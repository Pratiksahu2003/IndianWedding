<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultationSlot extends Model
{
    use BelongsToOrganization, HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'slot_id');
    }

    public function formattedDate(): string
    {
        return $this->date?->timezone(config('app.timezone'))->format('D, M j, Y')
            ?? (string) $this->date;
    }

    public function formattedStart(): string
    {
        return $this->formatTime($this->start_time);
    }

    public function formattedEnd(): string
    {
        return $this->formatTime($this->end_time);
    }

    public function label(): string
    {
        $base = $this->formattedDate().' · '.$this->formattedStart().'–'.$this->formattedEnd();

        if (filled($this->title)) {
            return $this->title.' — '.$base;
        }

        return $base;
    }

    public static function normalizeTime(?string $time): ?string
    {
        if (! filled($time)) {
            return null;
        }

        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable) {
            return $time;
        }
    }

    protected function formatTime(mixed $time): string
    {
        if (! filled($time)) {
            return '—';
        }

        try {
            return Carbon::parse((string) $time)->format('g:i A');
        } catch (\Throwable) {
            return (string) $time;
        }
    }
}
