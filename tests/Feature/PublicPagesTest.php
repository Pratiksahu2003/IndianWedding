<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Package;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_service_and_project_detail_pages_load(): void
    {
        $org = Organization::factory()->create(['is_active' => true, 'slug' => 'lumina-atelier']);
        $package = Package::factory()->create([
            'organization_id' => $org->id,
            'name' => 'Wedding Photography',
            'slug' => 'wedding-photography',
            'description' => 'We capture every candid smile.',
            'body' => 'Detailed wedding photography page.',
            'is_public' => true,
            'is_active' => true,
        ]);
        $item = PortfolioItem::query()->create([
            'organization_id' => $org->id,
            'title' => 'Vivaah',
            'slug' => 'vivaah-story',
            'story' => 'A Unik Studio wedding film.',
            'image_path' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
            'is_published' => true,
        ]);

        $this->get('/services/'.$package->slug)
            ->assertOk()
            ->assertSee('Wedding Photography')
            ->assertSee('Detailed wedding photography page');

        $this->get('/packages/'.$package->slug)->assertOk();

        $this->get('/projects/'.$item->slug)
            ->assertOk()
            ->assertSee('Vivaah')
            ->assertSee('A Unik Studio wedding film');

        $this->get('/portfolio/'.$item->slug)->assertOk();
        $this->get('/services/missing-service')->assertNotFound();
        $this->get('/projects/missing-project')->assertNotFound();
    }
}
