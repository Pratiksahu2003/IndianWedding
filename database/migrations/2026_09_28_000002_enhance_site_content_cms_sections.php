<?php

use App\Models\Organization;
use App\Models\SiteContent;
use App\Support\SiteCopy;
use App\Support\UnikStudioAssets;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_contents', function (Blueprint $table) {
            $table->string('section')->nullable()->after('group');
        });

        SiteContent::withoutTenant()->orderBy('id')->each(function (SiteContent $row): void {
            $row->update(['section' => $this->resolveSection($row->group, $row->key)]);
        });

        foreach (Organization::query()->where('is_active', true)->get() as $org) {
            $order = (int) SiteContent::withoutTenant()->where('organization_id', $org->id)->max('sort_order');
            foreach ($this->newRows() as $row) {
                SiteContent::withoutTenant()->updateOrCreate(
                    ['organization_id' => $org->id, 'key' => $row['key']],
                    [
                        'group' => $row['group'],
                        'section' => $row['section'],
                        'label' => $row['label'],
                        'value' => $row['value'],
                        'type' => $row['type'] ?? 'textarea',
                        'sort_order' => ++$order,
                    ]
                );
            }
            SiteCopy::forget($org->id);
        }

        SiteContent::withoutTenant()
            ->where(function ($query) {
                $query->whereIn('key', ['home.hero_image', 'about.hero_image'])
                    ->orWhere('key', 'like', 'home.slide_%_image');
            })
            ->update(['type' => 'image']);
    }

    public function down(): void
    {
        SiteContent::withoutTenant()
            ->whereIn('key', collect($this->newRows())->pluck('key'))
            ->delete();

        Schema::table('site_contents', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }

    protected function resolveSection(string $group, string $key): string
    {
        return match (true) {
            str_starts_with($key, 'brand.') => 'brand',
            str_starts_with($key, 'nav.') => 'nav',
            str_starts_with($key, 'footer.') => 'footer',
            str_starts_with($key, 'seo.') => 'seo',
            str_starts_with($key, 'social.') => 'social',
            str_starts_with($key, 'home.slide_') => 'hero',
            in_array($key, ['home.hero_image', 'home.kicker', 'home.headline', 'home.cta'], true) => 'hero',
            str_starts_with($key, 'home.story_') => 'story',
            str_starts_with($key, 'home.services_') => 'services',
            str_starts_with($key, 'home.moments_') || str_starts_with($key, 'home.stat_') => 'moments',
            str_starts_with($key, 'home.guide_') => 'guide',
            str_starts_with($key, 'home.portfolio_') => 'portfolio',
            str_starts_with($key, 'home.rsvp_') => 'rsvp',
            str_starts_with($key, 'home.feedbacks_') => 'feedbacks',
            str_starts_with($key, 'home.instagram_') => 'instagram',
            str_starts_with($key, 'lead_popup.') => 'popup',
            str_starts_with($key, 'about.') => 'content',
            str_starts_with($key, 'services.') => 'content',
            str_starts_with($key, 'gallery.') => 'content',
            str_starts_with($key, 'contact.') => 'content',
            str_starts_with($key, 'reservation.') => 'content',
            str_starts_with($key, 'testimonials.') => 'content',
            str_starts_with($key, 'faq.') => 'content',
            str_starts_with($key, 'team.') => 'content',
            str_starts_with($key, 'packages.') => 'content',
            default => 'content',
        };
    }

    /**
     * @return list<array{group: string, section: string, key: string, label: string, value: string, type?: string}>
     */
    protected function newRows(): array
    {
        $i = fn (string $g, string $s, string $k, string $l, string $v, string $t = 'textarea') => [
            'group' => $g,
            'section' => $s,
            'key' => $k,
            'label' => $l,
            'value' => $v,
            'type' => $t,
        ];

        $slides = [
            ['Wedding Photography & Films', 'Two-camera coverage of rituals, family emotions and reception nights — crafted into albums and films you will keep forever.', 'View weddings', '/services/wedding-photography', 'service-wedding.jpg'],
            ['Pre-Wedding Stories', 'Golden-hour portraits and cinematic outdoors — a romantic chapter before the vows, styled for print and film.', 'See pre-weddings', '/services/pre-wedding-shoot', 'service-prewedding.jpg'],
            ['Candid Documentation', 'Unposed laughter, quiet glances and real energy between rituals — photography that feels honest, never staged.', 'Explore candid', '/services/candid-shoot', 'service-candid.jpg'],
            ['Cinematography & Films', 'Movie-language storytelling with colour, motion and sound — highlight films and teaser reels for your wedding day.', 'Watch films', '/services/cinematography', 'service-cinema.jpg'],
            ['Birthdays & Music Videos', 'Celebrations and creative productions beyond weddings — birthdays, music videos and lifestyle shoots with the same craft.', 'Book a shoot', '/book-consultation', 'service-birthday.jpg'],
        ];

        $rows = [
            $i('lead_popup', 'popup', 'lead_popup.kicker', 'Popup kicker', 'Quick enquiry', 'input'),
            $i('lead_popup', 'popup', 'lead_popup.heading', 'Popup heading', 'Tell us your date', 'input'),
            $i('lead_popup', 'popup', 'lead_popup.body', 'Popup body', 'Share a few details and our team will get back within one working day.'),
            $i('lead_popup', 'popup', 'lead_popup.cta', 'Popup button', 'Send enquiry', 'input'),
            $i('testimonials', 'content', 'testimonials.heading', 'Page heading', 'Our Testimonials', 'input'),
            $i('testimonials', 'content', 'testimonials.intro', 'Page intro', 'Kind words from couples and families we have had the honour to work with.'),
            $i('faq', 'content', 'faq.heading', 'Page heading', 'Frequently Asked Questions', 'input'),
            $i('faq', 'content', 'faq.intro', 'Page intro', 'Answers to common questions about booking, coverage and delivery.'),
            $i('team', 'content', 'team.heading', 'Page heading', 'Our Team', 'input'),
            $i('team', 'content', 'team.intro', 'Page intro', 'Meet the photographers, filmmakers and editors behind Unik Studio.'),
            $i('packages', 'content', 'packages.heading', 'Page heading', 'Pricing & Packages', 'input'),
            $i('packages', 'content', 'packages.intro', 'Page intro', 'Transparent starting prices for our most popular photography and cinematography services.'),
            $i('about', 'content', 'about.hero_image', 'About hero image URL', UnikStudioAssets::url('hero-slide.png'), 'image'),
        ];

        foreach ($slides as $n => [$title, $body, $cta, $href, $imageFile]) {
            $num = $n + 1;
            $rows[] = $i('home', 'hero', "home.slide_{$num}_tab", "Slide {$num} tab label", $title, 'input');
            $rows[] = $i('home', 'hero', "home.slide_{$num}_title", "Slide {$num} title", $title, 'input');
            $rows[] = $i('home', 'hero', "home.slide_{$num}_body", "Slide {$num} description", $body);
            $rows[] = $i('home', 'hero', "home.slide_{$num}_cta", "Slide {$num} button", $cta, 'input');
            $rows[] = $i('home', 'hero', "home.slide_{$num}_href", "Slide {$num} link", $href, 'input');
            $rows[] = $i('home', 'hero', "home.slide_{$num}_image", "Slide {$num} image", UnikStudioAssets::url($imageFile), 'image');
        }

        return $rows;
    }
};
