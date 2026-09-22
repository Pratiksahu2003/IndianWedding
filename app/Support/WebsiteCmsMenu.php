<?php

namespace App\Support;

class WebsiteCmsMenu
{
    /**
     * @return list<array{id: string, label: string, icon?: string, children?: list<array{id: string, label: string}>}>
     */
    public static function tree(): array
    {
        return [
            [
                'id' => 'content',
                'label' => 'Page content',
                'children' => [
                    ['id' => 'brand', 'label' => 'Brand'],
                    ['id' => 'nav', 'label' => 'Navigation'],
                    ['id' => 'home', 'label' => 'Home'],
                    ['id' => 'about', 'label' => 'About'],
                    ['id' => 'services', 'label' => 'Services'],
                    ['id' => 'gallery', 'label' => 'Gallery page'],
                    ['id' => 'contact', 'label' => 'Contact'],
                    ['id' => 'reservation', 'label' => 'Booking'],
                    ['id' => 'footer', 'label' => 'Footer'],
                    ['id' => 'seo', 'label' => 'SEO'],
                ],
            ],
            [
                'id' => 'social',
                'label' => 'Social media',
            ],
            [
                'id' => 'team',
                'label' => 'Our team',
            ],
            [
                'id' => 'testimonials',
                'label' => 'Testimonials',
            ],
            [
                'id' => 'portfolio',
                'label' => 'Gallery images',
            ],
            [
                'id' => 'faq',
                'label' => 'FAQ',
            ],
        ];
    }

    public static function copyGroupLabel(string $group): string
    {
        foreach (self::tree() as $item) {
            if ($item['id'] !== 'content' || empty($item['children'])) {
                continue;
            }
            foreach ($item['children'] as $child) {
                if ($child['id'] === $group) {
                    return $child['label'];
                }
            }
        }

        return ucfirst(str_replace('_', ' ', $group));
    }
}
