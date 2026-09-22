<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\PortfolioItem;
use App\Models\SiteContent;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\SiteCopy;
use App\Support\Tenant;
use Illuminate\Database\Seeder;

class UnikStudioContentSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::query()->where('is_active', true)->firstOrFail();
        Tenant::set($org->id);

        $org->update([
            'name' => 'Unik Studio',
            'legal_name' => 'Unik Studio',
            'email' => 'info@unikstudio.in',
            'phone' => '+91 98183 61412',
            'website' => 'https://unikstudio.in',
            'address' => 'B-362, Gali No. 29, Mahavir Enclave, Part 2, Near Power House, New Delhi-110059',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'country' => 'India',
        ]);

        $copy = $this->copy();
        $order = 0;
        foreach ($copy as $row) {
            SiteContent::withoutTenant()->updateOrCreate(
                ['organization_id' => $org->id, 'key' => $row['key']],
                [
                    'group' => $row['group'],
                    'label' => $row['label'],
                    'value' => $row['value'],
                    'type' => $row['type'] ?? 'textarea',
                    'sort_order' => $order++,
                ]
            );
        }
        SiteCopy::forget($org->id);

        TeamMember::withoutTenant()->where('organization_id', $org->id)->delete();
        foreach ([
            ['Matthew Taylor', 'Founder & CEO'],
            ['Louisa Abadie', 'Writer, Photographer'],
            ['Amelia Harper', 'Photographer, Manager'],
            ['Mike Johnson', 'Writer, Manager'],
            ['Harper Wilson', 'Photographer, Manager'],
            ['Nicholas White', 'Photographer, Manager'],
        ] as $i => [$name, $role]) {
            TeamMember::query()->create([
                'organization_id' => $org->id,
                'name' => $name,
                'role' => $role,
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        Testimonial::withoutTenant()->where('organization_id', $org->id)->delete();
        foreach ([
            ['Marvin McKinney', 'Writer, Photographer, Manager', 'Laculis primis leo pharetra ac varius diam class odio, turpis nascetur gravida senectus sollicitudin lacus'],
            ['Louisa Abadie', 'Writer, Photographer, Manager', 'Fortunately, two seasoned digital marketers have a plan to make your brand succeed. In Faster, Smarter'],
            ['William Johnson', 'Writer, Photographer, Manager', 'pharetra ac varius diam class odio, turpis nascetur gravida senectus sollicitudin lacus cursus tortor diam'],
            ['Louisa Abadie', 'Harper Wilson', 'Fortunately, two seasoned digital marketers have a plan to make your brand succeed. In Faster, Smarter'],
        ] as $row) {
            Testimonial::query()->create([
                'organization_id' => $org->id,
                'author' => $row[0],
                'role' => $row[1],
                'quote' => $row[2],
                'is_published' => true,
                'rating' => 5,
            ]);
        }

        Faq::withoutTenant()->where('organization_id', $org->id)->delete();
        foreach ([
            ['How do I make a reservation?', 'Use Make Reservation on this website or call +91 98183 61412. You can also write to info@unikstudio.in, booking@unikstudio.in or wedding@unikstudio.in.'],
            ['Where is Unik Studio based?', 'B-362, Gali No. 29, Mahavir Enclave Part 2, Near Power House, New Delhi-110059, India.'],
            ['What do you shoot?', 'Wedding photography, cinematography, candid shoot, pre-wedding, birthday shoot and music video production.'],
            ['How experienced is the team?', 'Unik Studio has over 10 years of experience, professional equipment and techniques, a creative customized approach, on-time delivery and 100% client satisfaction.'],
        ] as $i => [$q, $a]) {
            Faq::query()->create([
                'organization_id' => $org->id,
                'question' => $q,
                'answer' => $a,
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        $images = [
            ['Pre-Wedding', 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=80', 'pre-wedding', 'Anjali & Abhay', 'A golden-hour pre-wedding in Delhi. Unik Studio followed the couple through quiet streets and open light, building a love story before the pheras.'],
            ['Wedding day', 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=1400&q=80', 'wedding', 'Vivaah', 'Every ritual, every glance, every family embrace — photographed as a film still so the wedding day never thins with time.'],
            ['Candid', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=1400&q=80', 'candid', 'Holding Hands', 'Unposed frames of laughter and nerves. Candid photography that lets people be themselves while we stay almost invisible.'],
            ['Portrait', 'https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=1400&q=80', 'wedding', 'Anjel~i', 'Bridal portraiture with soft light and editorial pacing. Glowing with happiness, framed for the album and the wall.'],
            ['Couple', 'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=1400&q=80', 'pre-wedding', 'Forever', 'Hands, vows in the making, and a promise photographed like cinema.'],
            ['Ceremony', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1400&q=80', 'wedding', 'Sacred fire', 'Mandap coverage with two photographers so rituals and family reactions are never missed.'],
            ['Love story', 'https://images.unsplash.com/photo-1529634597493-8c3742325d48?auto=format&fit=crop&w=1400&q=80', 'pre-wedding', 'New Romantic Love Story', 'A coming-soon chapter from Unik Studio — cinematic stills for couples who want movie language on their walls.'],
            ['Reception', 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1400&q=80', 'wedding', 'Beautiful Day', 'Dance floor, speeches, and late light. Reception photography that keeps the energy of the night.'],
            ['Details', 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=1400&q=80', 'wedding', 'The little things', 'Rings, florals, invitations and heirloom jewellery photographed as part of the story, not as a checklist.'],
            ['Film still', 'https://images.unsplash.com/photo-1522673607200-164e1b6ac4d5?auto=format&fit=crop&w=1400&q=80', 'cinematography', 'Cinematic Film Shoot', 'Slow motion, motivated camera moves and colour that belongs in a theatre — Unik cinematography.'],
            ['Candid joy', 'https://images.unsplash.com/photo-1529636798458-92182e662485?auto=format&fit=crop&w=1400&q=80', 'candid', 'Joy', 'Children, elders, stolen jokes. The wedding as it actually felt.'],
            ['Destination', 'https://images.unsplash.com/photo-1544078751-58fee2d8a03b?auto=format&fit=crop&w=1400&q=80', 'pre-wedding', 'Get Lost', 'Let’s find some beautiful place to get lost — destination frames for couples who travel to say yes.'],
        ];
        PortfolioItem::withoutTenant()->where('organization_id', $org->id)->delete();
        foreach ($images as $i => [$title, $url, $cat, $couple, $story]) {
            PortfolioItem::query()->create([
                'organization_id' => $org->id,
                'title' => $title,
                'slug' => \Illuminate\Support\Str::slug($title).'-'.($i + 1),
                'location' => 'New Delhi',
                'couple' => $couple,
                'story' => $story,
                'event_date' => now()->subMonths(12 - $i)->toDateString(),
                'image_path' => $url,
                'category' => $cat,
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        Package::withoutTenant()
            ->where('organization_id', $org->id)
            ->whereIn('slug', ['heritage', 'cinematic'])
            ->update(['is_public' => false, 'is_active' => false]);

        $services = [
            ['Wedding Photography', 'wedding-photography', 'We capture every candid smile, emotion and magical moment of your wedding day. Your wedding day is the beginning of a new journey, and we make sure no moment goes unnoticed. Our wedding photography captures the beauty, emotions, and traditions of your big day in timeless frames that you’ll cherish forever.', "Unik Studio has photographed weddings across Delhi and India for more than a decade.\n\nWe cover the pheras, the baraat, the quiet getting-ready hours and the last dance with a documentary eye and a cinematic frame. Two photographers when the family is large, one lead when the day is intimate.\n\nYou receive a curated gallery, an heirloom album option, and files that still look true ten years from now.\n\nBook a date, tell us the rituals that matter, and we build a shot list around your people — not a template.", 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=1800&q=80', 27500000],
            ['Cinematography', 'cinematography', 'Cinematic storytelling with slow motion, creative shots and movie-style edits. We bring your story to life with cinematic flair. Using advanced cameras, creative direction, and artistic editing, our cinematography transforms your special occasions into films that look straight out of a movie.', "Every Unik film is written in pictures first: a teaser, a highlight, and a longer family cut when you want it.\n\nWe shoot on cinema cameras with slow motion, motivated lighting and sound that keeps vows audible.\n\nColour, music and pacing are directed so the film feels like a memory, not a recap.\n\nShare your favourite songs and references — we will not drop a stock montage over your wedding.", 'https://images.unsplash.com/photo-1522673607200-164e1b6ac4d5?auto=format&fit=crop&w=1800&q=80', 42500000],
            ['Candid Shoot', 'candid-shoot', 'Natural, unposed moments filled with joy, laughter and real emotions. True beauty lies in unscripted moments. Our candid photography style captures raw emotions—smiles, tears, and laughter—without you even realizing the camera is there.', "Candid for us means no stiff posing, no stopping the baraat for a lineup unless you ask.\n\nWe work close and quiet, reading rooms, catching glances between rituals.\n\nThis is the service families remember — because it looks like the day they actually lived.", 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=1800&q=80', 8500000],
            ['Pre-Wedding Shoot', 'pre-wedding-shoot', 'Romantic locations and creative concepts to tell your love story. Celebrate your love before the big day with a customized pre-wedding shoot. Whether it’s at a romantic outdoor location or a creative studio setup, we create dreamy visuals that narrate your unique love story.', "We scout Delhi and nearby destinations for light, not clichés.\n\nWardrobe, location and time of day are planned with you. The set is small: photographer, sometimes a cinematographer, never a circus.\n\nStills and a short film can travel together so your save-the-date and cinema feel like one story.", 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1800&q=80', 12500000],
            ['Birthday Shoot', 'birthday-shoot', 'Beautifully capturing smiles, fun and unforgettable birthday memories. Birthdays are milestones that deserve to be remembered. We capture every joyful detail of your celebration.', "From first birthdays to milestone decades, we photograph cake, guests and the small chaos that makes a party feel real.\n\nCoverage can be a few hours or a full evening. Albums and reels are optional add-ons.\n\nTell us the theme; we match the light to it.", 'https://images.unsplash.com/photo-1464349095431-e9fe36c12562?auto=format&fit=crop&w=1800&q=80', 5500000],
            ['Music Video Shoot', 'music-video-shoot', 'Professional cinematic music videos that elevate your creative vision. Every artist deserves visuals that match the power of their music. Our professional music video production combines creativity, storytelling, and cinematic visuals.', "Unik Studio produces music videos with the same cinema kit we take to weddings — and a director’s plan.\n\nTreatment, locations, talent direction and colour are included in production conversations before the first slate.\n\nBring a track and a feeling. We will not deliver a random clip compilation.", 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1800&q=80', 18500000],
        ];
        foreach ($services as $i => [$name, $slug, $desc, $body, $cover, $price]) {
            $package = Package::withoutTenant()->updateOrCreate(
                ['organization_id' => $org->id, 'slug' => $slug],
                [
                    'name' => $name,
                    'description' => $desc,
                    'body' => $body,
                    'cover_image' => $cover,
                    'price' => $price,
                    'is_public' => true,
                    'is_active' => true,
                    'sort_order' => $i,
                    'includes_video' => str_contains($slug, 'cinema') || str_contains($slug, 'music'),
                    'includes_pre_wedding' => str_contains($slug, 'pre-wedding'),
                ]
            );
            if ($package->items()->doesntExist()) {
                foreach (['Professional equipment & techniques', 'Creative & customized approach', 'On-time delivery', '100% client satisfaction'] as $item) {
                    PackageItem::query()->create([
                        'organization_id' => $org->id,
                        'package_id' => $package->id,
                        'name' => $item,
                    ]);
                }
            }
        }
    }

    protected function copy(): array
    {
        $i = fn ($g, $k, $l, $v, $t = 'textarea') => ['group' => $g, 'key' => $k, 'label' => $l, 'value' => $v, 'type' => $t];

        return [
            $i('brand', 'brand.name', 'Studio name', 'Unik Studio', 'input'),
            $i('brand', 'brand.tagline', 'Tagline', 'Capturing Moments, Creating Memories', 'input'),
            $i('nav', 'nav.home', 'Nav: Home', 'Home', 'input'),
            $i('nav', 'nav.about', 'Nav: About', 'About', 'input'),
            $i('nav', 'nav.team', 'Nav: Our Team', 'Our Team', 'input'),
            $i('nav', 'nav.services', 'Nav: Service', 'Service', 'input'),
            $i('nav', 'nav.packages', 'Nav: Pricing', 'Pricing Page', 'input'),
            $i('nav', 'nav.reservation', 'Nav: Reservation', 'Make Reservation', 'input'),
            $i('nav', 'nav.gallery', 'Nav: Gallery', 'Gallery', 'input'),
            $i('nav', 'nav.contact', 'Nav: Contact', 'Contact', 'input'),
            $i('nav', 'nav.testimonials', 'Nav: Feedbacks', 'Feedbacks', 'input'),
            $i('nav', 'nav.faq', 'Nav: FAQ', 'FAQ', 'input'),

            $i('home', 'home.kicker', 'Hero kicker', 'Are getting married! Save the date:'),
            $i('home', 'home.headline', 'Hero headline', 'Capturing Moments, Creating Memories'),
            $i('home', 'home.cta', 'Hero button', 'MAKE RESERVATION', 'input'),
            $i('home', 'home.story_kicker', 'Story kicker', 'Unik Studio'),
            $i('home', 'home.story_heading', 'Story heading', 'OUR WEDDING STORY TO DATE'),
            $i('home', 'home.story_body', 'Story body', "Welcome to Unik Studio, your one-stop destination for premium photography and cinematography services. We specialize in turning your special occasions into timeless memories with creativity, passion, and professional expertise. Whether it’s a wedding, pre-wedding, birthday, or a music video shoot, our team ensures every frame tells a beautiful story."),
            $i('home', 'home.story_cta', 'Story button', 'MAKE RESERVATION', 'input'),
            $i('home', 'home.services_kicker', 'Services kicker', 'Our Services'),
            $i('home', 'home.services_heading', 'Services heading', 'Our Services'),
            $i('home', 'home.moments_kicker', 'Counters kicker', 'ENJOY OUR MOMENTS'),
            $i('home', 'home.moments_heading', 'Counters heading', 'COME WITH US'),
            $i('home', 'home.stat_1_value', 'Stat 1 value', '10+', 'input'),
            $i('home', 'home.stat_1_label', 'Stat 1 label', 'Weddings per year', 'input'),
            $i('home', 'home.stat_2_value', 'Stat 2 value', '10+', 'input'),
            $i('home', 'home.stat_2_label', 'Stat 2 label', 'Years of Celebration', 'input'),
            $i('home', 'home.stat_3_value', 'Stat 3 value', '100%', 'input'),
            $i('home', 'home.stat_3_label', 'Stat 3 label', 'Happy Clients', 'input'),
            $i('home', 'home.stat_4_value', 'Stat 4 value', '∞', 'input'),
            $i('home', 'home.stat_4_label', 'Stat 4 label', 'Countless Precious Moments', 'input'),
            $i('home', 'home.guide_kicker', 'Guide kicker', 'Wedding Guide & Inspiration'),
            $i('home', 'home.guide_1', 'Guide 1', '💌 The Request to Come to the Wedding'),
            $i('home', 'home.guide_2', 'Guide 2', '🌿 Let’s Find Some Beautiful Place to Get Lost'),
            $i('home', 'home.guide_3', 'Guide 3', '📝 What to Include on Your Wedding Invitation'),
            $i('home', 'home.guide_4', 'Guide 4', 'Wedding Invitation Wording Line by Line'),
            $i('home', 'home.guide_5', 'Guide 5', '🌸 The Request to Attend Beautiful Place'),
            $i('home', 'home.guide_note', 'Guide note', 'THERE IS SOMETHING FOR EVERYONE'),
            $i('home', 'home.portfolio_heading', 'Portfolio heading', 'WELCOME TO OUR PRE WEDDING & WEDDING'),
            $i('home', 'home.rsvp_kicker', 'RSVP kicker', 'LET US KNOW IF YOU COMING'),
            $i('home', 'home.rsvp_heading', 'RSVP heading', 'WE CANT WAIT TO SEE YOU!'),
            $i('home', 'home.rsvp_cta', 'RSVP button', 'MAKE RESERVATION', 'input'),
            $i('home', 'home.feedbacks_kicker', 'Testimonials kicker', 'FEEDBACKS'),
            $i('home', 'home.feedbacks_heading', 'Testimonials heading', 'Our Testimonials'),
            $i('home', 'home.instagram_handle', 'Instagram handle', '@unik_studioo', 'input'),
            $i('home', 'home.instagram_bio', 'Instagram bio', 'Cinematic Film Shoot ✷ Candid Photography ✷ Prewedding ✷ Wedding All Shoot Available.'),
            $i('home', 'home.instagram_1', 'Instagram caption 1', 'New Romantic Love Story is Coming Soon❤️🫶 #loveforever #couplelove❤️ #prewedding #loves #explorepage✨ #instapic . Book your slot now for best Cinematography & Photography. @unik_studioo Call ☎️: 9818361412 Gmail : unikstudio100@gmail.com'),
            $i('home', 'home.instagram_2', 'Instagram caption 2', 'Beautiful Day - Vivaah . #fallinginlove❤️ ❤️ #unikstudio #love Couple : @anjali_238_ & @abhay . Book your slot now for best Cinematography & Photography. @unik_studioo Call ☎️: 9818361412 Gmail : unikstudio100@gmail.com . #prewed #prewedding #wedding #preweddingphoto #preweddingshoot'),
            $i('home', 'home.instagram_3', 'Instagram caption 3', 'Holding Hands, forever and always❤️🫶 #loveforever #couplelove❤️ #prewedding #loves #explorepage✨ #instapic . Book your slot now for best Cinematography & Photography. @unik_studioo Call ☎️: 9818361412 Gmail : unikstudio100@gmail.com'),
            $i('home', 'home.instagram_4', 'Instagram caption 4', 'Anjel~i. Glowing with happiness 💖. #beautifullgirl ❤️ ❤️ #unikstudio #love Couple : @anjali_238_ & @abhay . Book your slot now for best Cinematography & Photography. @unik_studioo Call ☎️: 9818361412 Gmail : unikstudio100@gmail.com . #beautifullbride👰 #khubsurat #anjel #instapic'),

            $i('about', 'about.eyebrow', 'About email line', 'wedding@unikstudio.in', 'input'),
            $i('about', 'about.heading', 'About heading', 'About Us – Unik Studio'),
            $i('about', 'about.body', 'About body', "With over 10 years of experience, Unik Studio has become a trusted name in the world of photography and cinematography. We have captured thousands of smiles, countless weddings, and endless memories with passion and perfection. Our journey started with a simple belief – every picture should tell a story. Over the years, we have worked with clients across India, delivering creative and professional photography services that go beyond expectations. From intimate family celebrations to grand destination weddings, our team has the expertise, creativity, and advanced equipment to capture every detail in its most beautiful form. 10 years of excellence, 10 years of stories, 10 years of unforgettable memories. At Unik Studio, we don’t just take photos, we create moments that last a lifetime."),
            $i('about', 'about.values_heading', 'Values heading', 'OUR CORE VALUES'),
            $i('about', 'about.mission', 'Mission', 'Our mission is to capture emotions, moments, and memories in their purest form. We aim to turn every special occasion into a visual masterpiece that our clients can cherish for a lifetime.'),
            $i('about', 'about.vision', 'Vision', 'At Unik Studio, our destination is not just about reaching the top, but about creating a legacy in the world of photography and cinematography. We envision a future where every celebration, every milestone, and every story is captured with creativity, precision, and heartfelt emotions.'),
            $i('about', 'about.team_heading', 'Team heading', 'Our Team'),

            $i('services', 'services.eyebrow', 'Services email line', 'wedding@unikstudio.in', 'input'),
            $i('services', 'services.heading', 'Services heading', 'Our Services'),
            $i('services', 'services.intro', 'Services intro', 'At Unik Studio, we offer a wide range of professional photography and cinematography services designed to make your moments unforgettable. From weddings to birthdays and even music video productions, our team ensures every detail is captured with perfection.'),
            $i('services', 'services.featured_date', 'Featured date', 'Sunday June 16, 2024', 'input'),
            $i('services', 'services.featured_blurb', 'Featured blurb', 'Champagne wedding prime rib champagne centerpieces flowers fish mother. Beautiful cheers prime rib overpriced florist wedding glitter embarrassing coworkers magic glitter.'),
            $i('services', 'services.why_1', 'Why us 1', '10+ Years of Experience'),
            $i('services', 'services.why_2', 'Why us 2', 'Professional Equipment & Techniques'),
            $i('services', 'services.why_3', 'Why us 3', 'Creative & Customized Approach'),
            $i('services', 'services.why_4', 'Why us 4', 'On-Time Delivery'),
            $i('services', 'services.why_5', 'Why us 5', '100% Client Satisfaction'),

            $i('gallery', 'gallery.heading', 'Gallery heading', 'Gallery'),
            $i('gallery', 'gallery.prewedding_heading', 'Gallery section', 'Pre-Wedding Shoot'),
            $i('gallery', 'gallery.intro', 'Gallery intro', 'Welcome to our Pre Wedding & Wedding portfolio.'),

            $i('contact', 'contact.heading', 'Contact heading', 'Contact'),
            $i('contact', 'contact.office_label', 'Office label', 'Office Address', 'input'),
            $i('contact', 'contact.office', 'Office address', 'B-362, Gali, No. 29 Mahavir Enclave Part 2, Near By , Power House, Delhi, 110059, India'),
            $i('contact', 'contact.email_label', 'Email label', 'Email Address', 'input'),
            $i('contact', 'contact.emails', 'Emails', "info@unikstudio.in\nbooking@unikstudio.in\nwedding@unikstudio.in\nunikstudio100@gmail.com"),
            $i('contact', 'contact.phone_label', 'Phone label', 'Phone Number', 'input'),
            $i('contact', 'contact.phone_note', 'Phone note', '24/7 Anytime', 'input'),
            $i('contact', 'contact.phone', 'Phone', '+91 98183 61412', 'input'),
            $i('contact', 'contact.subscribe_heading', 'Subscribe heading', 'Subscribe Now'),
            $i('contact', 'contact.subscribe_note', 'Subscribe note', 'Don’t worry we don’t spam your email'),

            $i('reservation', 'reservation.heading', 'Reservation heading', 'Make Reservation'),
            $i('reservation', 'reservation.body', 'Reservation intro', 'LET US KNOW IF YOU COMING. WE CANT WAIT TO SEE YOU! Book your slot now for best Cinematography & Photography.'),

            $i('footer', 'footer.blurb', 'Footer blurb', 'Unik Studio — premium photography and cinematography. Cinematic Film Shoot ✷ Candid Photography ✷ Prewedding ✷ Wedding All Shoot Available.'),
            $i('footer', 'footer.contact_heading', 'Footer contact heading', 'Contact Info'),
            $i('footer', 'footer.links_heading', 'Footer links heading', 'Links'),
            $i('footer', 'footer.copyright', 'Copyright', '©UNIK Studio / ALL RIGHTS RESERVED | Managed By topnex media'),
            $i('seo', 'seo.title', 'Default SEO title', 'Unik Studio — Capturing Moments, Creating Memories', 'input'),
            $i('seo', 'seo.description', 'Default SEO description', 'Welcome to Unik Studio, your one-stop destination for premium photography and cinematography services in New Delhi. Wedding, pre-wedding, candid, birthday and music video shoots.'),
        ];
    }
}
