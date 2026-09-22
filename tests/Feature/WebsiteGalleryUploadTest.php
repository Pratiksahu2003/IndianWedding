<?php

namespace Tests\Feature;

use App\Livewire\Platform\WebsiteEditor;
use App\Models\Organization;
use App\Models\PortfolioItem;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteGalleryUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_admin_can_upload_gallery_images_up_to_two_megabytes(): void
    {
        Storage::fake('public');

        [$org, $admin] = $this->studioAdmin();

        $file = UploadedFile::fake()->image('ceremony.jpg', 800, 600)->size(1900);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('portfolio_title', 'Ceremony')
            ->set('portfolio_category', 'wedding')
            ->set('portfolio_uploads', [$file])
            ->call('addPortfolio')
            ->assertHasNoErrors();

        $item = PortfolioItem::query()->first();

        $this->assertNotNull($item);
        $this->assertSame('Ceremony', $item->title);
        $this->assertSame('wedding', $item->category);
        Storage::disk('public')->assertExists($item->image_path);
        $this->assertStringContainsString('/storage/', $item->imageUrl());
    }

    public function test_gallery_upload_rejects_images_larger_than_two_megabytes(): void
    {
        Storage::fake('public');

        [$org, $admin] = $this->studioAdmin();

        $file = UploadedFile::fake()->image('huge.jpg', 800, 600)->size(2049);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('portfolio_title', 'Too large')
            ->set('portfolio_uploads', [$file])
            ->call('addPortfolio')
            ->assertHasErrors(['portfolio_uploads.0']);

        $this->assertSame(0, PortfolioItem::query()->count());
    }

    public function test_gallery_image_can_still_be_added_from_a_url(): void
    {
        [$org, $admin] = $this->studioAdmin();

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(WebsiteEditor::class)
            ->set('portfolio_title', 'From URL')
            ->set('portfolio_image', 'https://images.example.com/wedding.jpg')
            ->call('addPortfolio')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolio_items', [
            'title' => 'From URL',
            'image_path' => 'https://images.example.com/wedding.jpg',
        ]);
    }

    /**
     * @return array{0: Organization, 1: User}
     */
    private function studioAdmin(): array
    {
        $org = Organization::factory()->create(['is_active' => true]);
        $admin = User::factory()->create();
        $org->users()->attach($admin->id, ['role' => 'studio_admin', 'accepted_at' => now()]);
        Tenant::set($org->id);

        return [$org, $admin];
    }
}
