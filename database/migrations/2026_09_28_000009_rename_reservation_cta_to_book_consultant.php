<?php

use App\Models\SiteContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $replacements = [
        'nav.reservation' => 'Book Consultant',
        'home.cta' => 'BOOK CONSULTANT',
        'home.story_cta' => 'BOOK CONSULTANT',
        'home.rsvp_cta' => 'BOOK CONSULTANT',
        'reservation.heading' => 'Book Consultant',
    ];

    public function up(): void
    {
        foreach ($this->replacements as $key => $value) {
            SiteContent::withoutTenant()
                ->where('key', $key)
                ->whereIn('value', [
                    'Make Reservation',
                    'MAKE RESERVATION',
                    'Make reservation',
                ])
                ->update(['value' => $value]);
        }
    }

    public function down(): void
    {
        $reverse = [
            'nav.reservation' => 'Make Reservation',
            'home.cta' => 'MAKE RESERVATION',
            'home.story_cta' => 'MAKE RESERVATION',
            'home.rsvp_cta' => 'MAKE RESERVATION',
            'reservation.heading' => 'Make Reservation',
        ];

        foreach ($reverse as $key => $value) {
            SiteContent::withoutTenant()
                ->where('key', $key)
                ->where('value', $this->replacements[$key])
                ->update(['value' => $value]);
        }
    }
};
