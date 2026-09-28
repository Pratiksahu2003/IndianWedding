<?php

use App\Models\Organization;
use App\Models\SiteContent;
use App\Support\SiteCopy;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['production', 'content', 'production.eyebrow', 'Page eyebrow', 'Film & Production', 'input'],
            ['production', 'content', 'production.heading', 'Page heading', 'Wedding & Production Films', 'input'],
            ['production', 'content', 'production.intro', 'Page intro', 'Cinematic wedding films, music videos, and event productions — crafted with the same storytelling eye as our photography.', 'textarea'],
            ['production', 'content', 'production.services_heading', 'Services block heading', 'What we produce', 'input'],
            ['production', 'content', 'production.service_1_title', 'Service 1 title', 'Wedding cinematography', 'input'],
            ['production', 'content', 'production.service_1_body', 'Service 1 description', 'Highlight films, full-length documentaries, and teaser reels for your wedding day.', 'textarea'],
            ['production', 'content', 'production.service_2_title', 'Service 2 title', 'Pre-wedding films', 'input'],
            ['production', 'content', 'production.service_2_body', 'Service 2 description', 'Romantic outdoor shoots turned into cinematic short films.', 'textarea'],
            ['production', 'content', 'production.service_3_title', 'Service 3 title', 'Music videos', 'input'],
            ['production', 'content', 'production.service_3_body', 'Service 3 description', 'Professional music video production with creative direction and colour grading.', 'textarea'],
            ['production', 'content', 'production.service_4_title', 'Service 4 title', 'Candid & documentary', 'input'],
            ['production', 'content', 'production.service_4_body', 'Service 4 description', 'Unposed moments and behind-the-scenes storytelling.', 'textarea'],
            ['production', 'content', 'production.service_5_title', 'Service 5 title', 'Birthday & events', 'input'],
            ['production', 'content', 'production.service_5_body', 'Service 5 description', 'Celebrations, sangeet films, and milestone event coverage.', 'textarea'],
            ['production', 'content', 'production.service_6_title', 'Service 6 title', 'Corporate & brand films', 'input'],
            ['production', 'content', 'production.service_6_body', 'Service 6 description', 'Brand stories, promos, and corporate event films.', 'textarea'],
            ['production', 'content', 'production.why_heading', 'Why us heading', 'Why Unik Studio for production', 'input'],
            ['production', 'content', 'production.why_1', 'Why us 1', 'Cinema cameras & professional sound', 'input'],
            ['production', 'content', 'production.why_2', 'Why us 2', 'Story-led editing & colour grading', 'input'],
            ['production', 'content', 'production.why_3', 'Why us 3', 'On-time delivery with review rounds', 'input'],
            ['production', 'content', 'production.why_4', 'Why us 4', 'Same team as our wedding photography', 'input'],
            ['production', 'content', 'production.why_5', 'Why us 5', 'Deliverables for social, TV, and archive', 'input'],
            ['nav', 'nav', 'nav.production', 'Nav label: Production', 'Production', 'input'],
        ];

        foreach (Organization::query()->where('is_active', true)->get() as $org) {
            $order = (int) SiteContent::withoutTenant()->where('organization_id', $org->id)->max('sort_order');
            foreach ($rows as [$group, $section, $key, $label, $value, $type]) {
                SiteContent::withoutTenant()->updateOrCreate(
                    ['organization_id' => $org->id, 'key' => $key],
                    [
                        'group' => $group,
                        'section' => $section,
                        'label' => $label,
                        'value' => $value,
                        'type' => $type ?? 'textarea',
                        'sort_order' => ++$order,
                    ]
                );
            }
            SiteCopy::forget($org->id);
        }
    }

    public function down(): void
    {
        SiteContent::withoutTenant()
            ->where('group', 'production')
            ->orWhere('key', 'nav.production')
            ->delete();
    }
};
