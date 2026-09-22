<?php

namespace Database\Seeders;

use App\Actions\ConvertLeadToBooking;
use App\Actions\RecordPayment;
use App\Enums\LeadStatus;
use App\Enums\Role;
use App\Models\ConsultationSlot;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Plan;
use App\Models\PortfolioItem;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['Starter', 'starter', 5, 20, 50, 5120],
            ['Professional', 'professional', 15, 80, 200, 51200],
            ['Business', 'business', 40, 250, 800, 204800],
            ['Enterprise', 'enterprise', 200, 2000, 5000, 1048576],
        ];
        foreach ($plans as [$name, $slug, $users, $projects, $clients, $storage]) {
            Plan::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'price_monthly' => 0,
                'max_users' => $users,
                'max_projects' => $projects,
                'max_clients' => $clients,
                'max_storage_mb' => $storage,
                'features' => ['crm', 'gallery', 'invoices'],
            ]);
        }

        $org = Organization::query()->updateOrCreate(['slug' => 'lumina-atelier'], [
            'name' => 'Lumina Atelier',
            'plan_id' => Plan::query()->where('slug', 'professional')->value('id'),
            'email' => 'studio@demo.vedmint.com',
            'phone' => '+91 98765 00001',
            'website' => 'https://demo.vedmint.com',
            'city' => 'Jaipur',
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'invoice_prefix' => 'LUM',
            'brand_primary' => '#C4A574',
            'is_active' => true,
            'ulid' => (string) Str::ulid(),
        ]);

        Tenant::set($org->id);

        $roles = [
            'admin@demo.vedmint.com' => [Role::StudioAdmin, 'Aanya Kapoor'],
            'manager@demo.vedmint.com' => [Role::Admin, 'Rohan Mehta'],
            'photographer@demo.vedmint.com' => [Role::Photographer, 'Ishaan Rao'],
            'videographer@demo.vedmint.com' => [Role::Videographer, 'Meera Shah'],
            'editor@demo.vedmint.com' => [Role::Editor, 'Kabir Anand'],
            'client@demo.vedmint.com' => [Role::Client, 'Aditi Sharma'],
        ];

        $users = [];
        foreach ($roles as $email => [$role, $name]) {
            $user = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'ulid' => (string) Str::ulid(),
                'is_active' => true,
            ]);
            $org->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => $role->value,
                    'is_owner' => $role === Role::StudioAdmin,
                    'accepted_at' => now(),
                ],
            ]);
            $users[$role->value] = $user;
        }

        $source = LeadSource::query()->firstOrCreate(
            ['organization_id' => $org->id, 'slug' => 'website'],
            ['name' => 'Website']
        );

        $heritage = Package::query()->updateOrCreate(
            ['organization_id' => $org->id, 'slug' => 'heritage'],
            [
                'name' => 'Heritage',
                'description' => 'A full-day stills collection with two photographers and an heirloom album.',
                'price' => 27500000,
                'duration_hours' => 12,
                'photography_hours' => 12,
                'photographer_count' => 2,
                'videographer_count' => 0,
                'edited_photos' => 400,
                'includes_album' => true,
                'is_public' => true,
                'is_active' => true,
            ]
        );
        Package::query()->updateOrCreate(
            ['organization_id' => $org->id, 'slug' => 'cinematic'],
            [
                'name' => 'Cinematic',
                'description' => 'Stills and a wedding film, including a pre-wedding evening.',
                'price' => 42500000,
                'duration_hours' => 14,
                'photographer_count' => 2,
                'videographer_count' => 1,
                'edited_photos' => 500,
                'includes_video' => true,
                'includes_pre_wedding' => true,
                'is_public' => true,
                'is_active' => true,
            ]
        );

        foreach ([
            ['author' => 'Anika & Veer', 'quote' => 'They photographed like they were guests who happened to see everything.'],
            ['author' => 'Sara Ali', 'quote' => 'The gallery felt like a film stills department, not a dump of eight thousand frames.'],
        ] as $row) {
            Testimonial::query()->create($row + ['organization_id' => $org->id, 'role' => 'Couple', 'is_published' => true]);
        }

        $images = [
            'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1529634597493-8c3742325d48?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1200&q=80',
        ];
        foreach ($images as $i => $url) {
            PortfolioItem::query()->create([
                'organization_id' => $org->id,
                'title' => 'Frame '.($i + 1),
                'location' => 'Udaipur',
                'image_path' => $url,
                'category' => 'wedding',
                'is_published' => true,
                'sort_order' => $i,
            ]);
        }

        Faq::query()->create([
            'organization_id' => $org->id,
            'question' => 'When do we receive the preview?',
            'answer' => 'A watermarked preview is typically released after the editing milestone is paid.',
            'is_published' => true,
        ]);

        ConsultationSlot::query()->create([
            'organization_id' => $org->id,
            'staff_user_id' => $users[Role::Admin->value]->id,
            'date' => now()->addDays(4)->toDateString(),
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'timezone' => 'Asia/Kolkata',
            'is_available' => true,
        ]);

        $statuses = LeadStatus::cases();
        foreach ($statuses as $i => $status) {
            Lead::query()->create([
                'organization_id' => $org->id,
                'lead_source_id' => $source->id,
                'package_id' => $heritage->id,
                'assigned_to' => $users[Role::Admin->value]->id,
                'lead_number' => sprintf('LD-001-%s', str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)),
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->numerify('+91##########'),
                'whatsapp' => fake()->numerify('+91##########'),
                'wedding_date' => now()->addMonths(4 + $i),
                'venue' => 'Umaid Bhawan',
                'city' => 'Jodhpur',
                'guest_count' => 180,
                'budget' => 400000,
                'services' => ['Photography', 'Album'],
                'status' => $status,
                'notes' => 'Demo lead — identifiable sample data.',
                'follow_up_at' => now()->addDay(),
            ]);
        }

        $bookable = Lead::query()->create([
            'organization_id' => $org->id,
            'lead_source_id' => $source->id,
            'package_id' => $heritage->id,
            'assigned_to' => $users[Role::Admin->value]->id,
            'lead_number' => 'LD-001-BOOK',
            'name' => 'Aditi Sharma',
            'email' => 'client@demo.vedmint.com',
            'phone' => '+919876500099',
            'whatsapp' => '+919876500099',
            'wedding_date' => now()->addMonths(5),
            'venue' => 'Lake Pichola',
            'city' => 'Udaipur',
            'guest_count' => 220,
            'budget' => 550000,
            'status' => LeadStatus::Negotiation,
            'notes' => 'Primary demo booking.',
        ]);

        $project = app(ConvertLeadToBooking::class)->handle($bookable);
        $project->customer->update(['user_id' => $users[Role::Client->value]->id]);

        $advance = $project->paymentMilestones()->orderBy('sort_order')->first();
        if ($advance) {
            app(RecordPayment::class)->handle($advance, $advance->amount, ['gateway' => 'manual', 'notes' => 'Demo advance']);
        }

        $this->call(UnikStudioContentSeeder::class);

        $this->command?->info('Demo studio ready. Login admin@demo.vedmint.com / password');
    }
}
