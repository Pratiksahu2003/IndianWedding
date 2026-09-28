<?php

use App\Models\Organization;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\SiteContent;
use App\Support\Money;
use App\Support\SiteCopy;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Organization::query()->where('is_active', true)->get() as $org) {
            $this->seedCopy($org->id);
            $this->seedWeddingPackages($org->id);
            $this->seedProductionPackages($org->id);
            $this->seedAddons($org->id);
            SiteCopy::forget($org->id);
        }
    }

    protected function seedCopy(int $orgId): void
    {
        $order = (int) SiteContent::withoutTenant()->where('organization_id', $orgId)->max('sort_order');

        $rows = [
            ['brand', 'brand', 'brand.tagline', 'Tagline', 'Your Story. Our Lens. Forever.', 'input'],
            ['services', 'content', 'services.eyebrow', 'Services eyebrow', 'Wedding Photography • Cinematography • Creative Production', 'input'],
            ['services', 'content', 'services.heading', 'Services heading', 'Our Services', 'input'],
            ['services', 'content', 'services.intro', 'Services intro', 'A professional service portfolio covering wedding packages and end-to-end production services for music videos, films, web series, serials, corporate shoots and product campaigns.', 'textarea'],
            ['services', 'content', 'services.why_1', 'Why us 1', '10+ Years of Experience', 'input'],
            ['services', 'content', 'services.why_2', 'Why us 2', 'Professional Equipment & Techniques', 'input'],
            ['services', 'content', 'services.why_3', 'Why us 3', 'Creative & Customized Approach', 'input'],
            ['services', 'content', 'services.why_4', 'Why us 4', 'On-Time Delivery', 'input'],
            ['services', 'content', 'services.why_5', 'Why us 5', '100% Client Satisfaction', 'input'],
            ['packages', 'content', 'packages.heading', 'Page heading', 'Wedding Photography & Videography Packages', 'input'],
            ['packages', 'content', 'packages.intro', 'Page intro', 'Every wedding deserves to be remembered beautifully. Our packages combine photography, cinematic videography and professionally edited memories, with coverage designed around the scale of your celebration.', 'textarea'],
            ['packages', 'content', 'packages.addons_heading', 'Add-ons heading', 'Wedding Add-On Services', 'input'],
            ['packages', 'content', 'packages.addons_note', 'Add-ons note', 'Destination travel, accommodation, venue permissions and other location-specific expenses may be charged separately where applicable. Final package scope can be customized according to the event schedule and requirements.', 'textarea'],
            ['production', 'content', 'production.eyebrow', 'Page eyebrow', 'Production & Creative Services', 'input'],
            ['production', 'content', 'production.heading', 'Page heading', 'Production & Creative Services', 'input'],
            ['production', 'content', 'production.intro', 'Page intro', 'From concept to screen, we provide end-to-end production solutions for music videos, short films, web series, television/serial shoots, corporate films, product shoots and digital content.', 'textarea'],
            ['production', 'content', 'production.services_heading', 'Services block heading', 'What we produce', 'input'],
            ['production', 'content', 'production.team_heading', 'Team heading', 'Production Team', 'input'],
            ['production', 'content', 'production.workflow_heading', 'Workflow heading', 'Production Workflow', 'input'],
            ['production', 'content', 'production.tagline', 'Tagline', 'Your Story. Our Lens. Forever.', 'input'],
            ['production', 'content', 'production.tagline_body', 'Tagline body', "We don't just shoot videos — we build visual experiences. Whether it is a wedding, music video, short film, web series, television production, corporate film or product campaign, our team manages the complete journey from idea to final screen.", 'textarea'],
            ['production', 'content', 'production.step_1_title', 'Step 1 title', 'Concept', 'input'],
            ['production', 'content', 'production.step_1_body', 'Step 1 body', 'Understand the idea, objective, audience and creative requirements.', 'textarea'],
            ['production', 'content', 'production.step_2_title', 'Step 2 title', 'Pre-Production', 'input'],
            ['production', 'content', 'production.step_2_body', 'Step 2 body', 'Script, storyboard, locations, cast, crew, equipment and shooting schedule are planned.', 'textarea'],
            ['production', 'content', 'production.step_3_title', 'Step 3 title', 'Production', 'input'],
            ['production', 'content', 'production.step_3_body', 'Step 3 body', 'Professional team executes the shoot with cameras, lighting, sound and required equipment.', 'textarea'],
            ['production', 'content', 'production.step_4_title', 'Step 4 title', 'Post-Production', 'input'],
            ['production', 'content', 'production.step_4_body', 'Step 4 body', 'Editing, color grading, sound design, music synchronization, VFX and graphics are completed.', 'textarea'],
            ['production', 'content', 'production.step_5_title', 'Step 5 title', 'Final Delivery', 'input'],
            ['production', 'content', 'production.step_5_body', 'Step 5 body', 'Final content is delivered in formats suitable for YouTube, Instagram, OTT, websites, television and digital advertising.', 'textarea'],
        ];

        $team = [
            'Team Leader / Production Lead — Overall production coordination, team management, scheduling and client communication.',
            'Director — Creative vision, storytelling, scene direction and visual execution.',
            'Candid Photographer — Natural moments, emotions, expressions and behind-the-scenes photography.',
            'Cinematographer / Camera Operator — Camera operation, framing, movement and cinematic visual production.',
            'Drone Operator — Aerial photography and cinematography for locations, events and cinematic sequences.',
            'Light & Technical Crew — Lighting, camera accessories, technical setup and on-location requirements.',
            'Editor — Films, reels, shorts, advertisements and digital content editing.',
            'Colorist — Color correction and cinematic color grading.',
            'Sound Team — Production audio, dialogue recording, sound design and final audio preparation.',
        ];

        foreach ($team as $i => $line) {
            $rows[] = ['production', 'content', 'production.team_'.($i + 1), 'Team member '.($i + 1), $line, 'input'];
        }

        foreach ($rows as [$group, $section, $key, $label, $value, $type]) {
            SiteContent::withoutTenant()->updateOrCreate(
                ['organization_id' => $orgId, 'key' => $key],
                [
                    'group' => $group,
                    'section' => $section,
                    'label' => $label,
                    'value' => $value,
                    'type' => $type,
                    'sort_order' => ++$order,
                ]
            );
        }
    }

    protected function seedWeddingPackages(int $orgId): void
    {
        $packages = [
            [
                'slug' => 'silver-wedding',
                'name' => 'Silver',
                'description' => 'Perfect for intimate and small weddings',
                'price' => 49999,
                'duration_hours' => 8,
                'photographer_count' => 2,
                'videographer_count' => 1,
                'edited_photos' => 300,
                'includes_album' => false,
                'includes_video' => true,
                'includes_pre_wedding' => false,
                'includes_drone' => false,
                'items' => [
                    'Traditional Photography — 1 Photographer',
                    'Candid Photography — 1 Photographer',
                    'Traditional Videography — 1 Videographer',
                    'Wedding Highlight Video — 3–4 Minutes',
                    'Full Wedding Ceremony Video',
                    '2 Instagram/Reels Videos',
                    '300+ Professionally Edited Photos',
                    'Digital Delivery via Google Drive / Online Gallery',
                    'Up to 8 Hours Coverage',
                    'Professional Photography Team',
                ],
            ],
            [
                'slug' => 'gold-wedding',
                'name' => 'Gold',
                'description' => 'Popular package for complete wedding coverage',
                'price' => 79999,
                'duration_hours' => 10,
                'photographer_count' => 2,
                'videographer_count' => 1,
                'edited_photos' => 500,
                'includes_album' => true,
                'includes_video' => true,
                'includes_pre_wedding' => true,
                'includes_drone' => false,
                'items' => [
                    'Candid Photography — 1 Photographer',
                    'Traditional Photography — 1 Photographer',
                    'Cinematic Videography — 1 Videographer',
                    'Wedding Highlight Film — 5–7 Minutes',
                    'Full Wedding Ceremony Video',
                    '4 Instagram/Reels Videos',
                    'Pre-Wedding / Couple Shoot',
                    '500+ Professionally Edited Photos',
                    'Premium Wedding Album — 30 Pages',
                    'Digital Delivery via Google Drive / Online Gallery',
                    'Up to 10 Hours Coverage',
                    'Dedicated Photography Team',
                ],
            ],
            [
                'slug' => 'platinum-wedding',
                'name' => 'Platinum',
                'description' => 'Premium cinematic wedding storytelling',
                'price' => 149999,
                'duration_hours' => 12,
                'photographer_count' => 3,
                'videographer_count' => 2,
                'edited_photos' => 800,
                'includes_album' => true,
                'includes_video' => true,
                'includes_pre_wedding' => true,
                'includes_drone' => true,
                'items' => [
                    'Candid Photography — 2 Photographers',
                    'Traditional Photography — 1 Photographer',
                    'Cinematic Videography — 2 Videographers',
                    'Drone Photography & Cinematography',
                    'Cinematic Wedding Film — 8–12 Minutes',
                    'Full Wedding Ceremony Video',
                    '6 Premium Instagram/Reels Videos',
                    'Pre-Wedding Couple Shoot',
                    'Pre-Wedding Cinematic Video',
                    '800+ Professionally Edited Photos',
                    'Premium Photobook — 40 Pages',
                    'Wedding Teaser — 60–90 Seconds',
                    'High-Resolution Digital Delivery',
                    'Full-Day Coverage',
                    'Dedicated Creative Team',
                ],
            ],
            [
                'slug' => 'destination-wedding',
                'name' => 'Destination',
                'description' => 'Luxury destination wedding experience',
                'price' => 349999,
                'duration_hours' => 24,
                'photographer_count' => 3,
                'videographer_count' => 2,
                'edited_photos' => 1000,
                'includes_album' => true,
                'includes_video' => true,
                'includes_pre_wedding' => true,
                'includes_drone' => true,
                'items' => [
                    'Candid Photography — 2 Photographers',
                    'Traditional Photography — 1 Photographer',
                    'Cinematic Videography — 2 Videographers',
                    'Drone Photography & Cinematography',
                    'Luxury Wedding Film — 12–15 Minutes',
                    'Full Wedding Ceremony Films',
                    'Pre-Wedding Couple Shoot',
                    'Cinematic Pre-Wedding Film',
                    '10 Premium Instagram/Reels Videos',
                    'Wedding Teaser — 1–2 Minutes',
                    '1,000+ Professionally Edited Photos',
                    'Luxury Premium Wedding Album — 50 Pages',
                    'Complete Digital Wedding Archive',
                    'Multi-Location Coverage',
                    'Multi-Day Wedding Coverage',
                    'Dedicated Production & Creative Team',
                ],
            ],
        ];

        foreach ($packages as $i => $data) {
            $items = $data['items'];
            unset($data['items']);

            $package = Package::withoutTenant()->updateOrCreate(
                ['organization_id' => $orgId, 'slug' => $data['slug']],
                [
                    ...$data,
                    'package_type' => 'wedding',
                    'price' => Money::fromMajor($data['price']),
                    'is_public' => true,
                    'is_active' => true,
                    'sort_order' => $i,
                ]
            );

            $this->syncItems($orgId, $package, $items);
        }
    }

    protected function seedProductionPackages(int $orgId): void
    {
        $packages = [
            [
                'slug' => 'music-video-production',
                'name' => 'Music Video Production',
                'description' => 'We bring music to life through strong visual storytelling and professional production.',
                'items' => [
                    'Concept & Story Development', 'Script & Screenplay', 'Location Planning',
                    'Professional Camera & Lighting', 'Cinematography & Direction', 'Drone Cinematography',
                    'Artist Coordination', 'Video Editing & Color Grading', 'Sound Sync & Final Master',
                    'Social Media Reels & Shorts',
                ],
            ],
            [
                'slug' => 'short-film-production',
                'name' => 'Short Film Production',
                'description' => 'Complete short-film production support from story development to final delivery.',
                'items' => [
                    'Story & Concept Development', 'Script & Screenplay', 'Casting Coordination',
                    'Location Scouting', 'Production Design', 'Cinematography', 'Lighting & Sound',
                    'Direction', 'Editing', 'Color Grading', 'Background Music & Sound Design', 'Final Film Master',
                ],
            ],
            [
                'slug' => 'web-series-production',
                'name' => 'Web Series Production',
                'description' => 'End-to-end episodic production designed for digital and OTT-style content.',
                'items' => [
                    'Concept Development', 'Story & Screenplay', 'Episode Planning', 'Casting',
                    'Location Management', 'Production Planning', 'Camera & Lighting', 'Direction',
                    'Sound Recording', 'Multi-Episode Editing', 'Color Grading', 'Trailer & Promo Creation', 'Episode Mastering',
                ],
            ],
            [
                'slug' => 'serial-television-production',
                'name' => 'Serial & Television Production',
                'description' => 'Professional production support for television and episodic content.',
                'items' => [
                    'Pre-Production Planning', 'Episode Scheduling', 'Crew Management', 'Camera Department',
                    'Lighting Department', 'Location Coordination', 'Artist Coordination', 'Direction Support',
                    'Production Management', 'Sound Recording', 'Video Editing', 'Color Correction',
                    'Promotional Content', 'Episode Delivery',
                ],
            ],
            [
                'slug' => 'corporate-shoot',
                'name' => 'Corporate Shoot',
                'description' => 'Professional visual content for companies, institutions and brands.',
                'items' => [
                    'Corporate Brand Films', 'Company Profile Videos', 'CEO / Founder Interviews',
                    'Office & Factory Shoots', 'Employee Stories', 'Corporate Events', 'Training Videos',
                    'Product Demonstrations', 'Promotional Videos', 'Customer Testimonials',
                    'Recruitment Videos', 'Social Media Corporate Content',
                ],
            ],
            [
                'slug' => 'product-shoot',
                'name' => 'Product Shoot',
                'description' => 'High-quality visual production for e-commerce brands, manufacturers and advertising campaigns.',
                'items' => [
                    'Product Photography', 'Product Videography', 'Lifestyle Product Shoot', 'Studio Shoot',
                    'Creative Product Videos', '360° Product Shots', 'Macro Shots', 'Product Demonstration Videos',
                    'Reels & Shorts', 'Promotional Videos', 'Product Advertisement Films', 'Editing & Color Grading',
                ],
            ],
        ];

        foreach ($packages as $i => $data) {
            $items = $data['items'];
            unset($data['items']);

            $package = Package::withoutTenant()->updateOrCreate(
                ['organization_id' => $orgId, 'slug' => $data['slug']],
                [
                    ...$data,
                    'package_type' => 'production',
                    'price' => 0,
                    'duration_hours' => 8,
                    'photographer_count' => 0,
                    'videographer_count' => 1,
                    'edited_photos' => 0,
                    'includes_album' => false,
                    'includes_video' => true,
                    'is_public' => true,
                    'is_active' => true,
                    'sort_order' => 100 + $i,
                ]
            );

            $this->syncItems($orgId, $package, $items);
        }
    }

    protected function seedAddons(int $orgId): void
    {
        $addons = [
            'Pre-Wedding Shoot',
            'Drone Photography & Cinematography',
            'Additional Photographer / Videographer',
            'Premium Wedding Album',
            'Wedding Invitation Video',
            'Instagram Reels Package',
            'Same-Day Edit / Highlight',
            'Live Wedding Streaming',
            'Additional Wedding Day Coverage',
            'Destination Wedding Coverage',
        ];

        foreach ($addons as $i => $name) {
            $package = Package::withoutTenant()->updateOrCreate(
                ['organization_id' => $orgId, 'slug' => 'addon-'.str($name)->slug()],
                [
                    'name' => $name,
                    'package_type' => 'addon',
                    'description' => 'Available as an add-on to any wedding package.',
                    'price' => 0,
                    'duration_hours' => 0,
                    'photographer_count' => 0,
                    'videographer_count' => 0,
                    'edited_photos' => 0,
                    'includes_album' => false,
                    'includes_video' => false,
                    'is_public' => true,
                    'is_active' => true,
                    'sort_order' => 200 + $i,
                ]
            );

            if ($package->items()->doesntExist()) {
                PackageItem::query()->create([
                    'organization_id' => $orgId,
                    'package_id' => $package->id,
                    'name' => 'Contact us for pricing and availability',
                    'sort_order' => 0,
                ]);
            }
        }
    }

    protected function syncItems(int $orgId, Package $package, array $items): void
    {
        $package->items()->delete();

        foreach ($items as $i => $name) {
            PackageItem::query()->create([
                'organization_id' => $orgId,
                'package_id' => $package->id,
                'name' => $name,
                'sort_order' => $i,
            ]);
        }
    }

    public function down(): void
    {
        // Content updates are non-destructive; packages remain editable in studio.
    }
};
