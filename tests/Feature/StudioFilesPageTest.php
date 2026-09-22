<?php

namespace Tests\Feature;

use App\Livewire\Studio\Files\Index;
use App\Models\Customer;
use App\Models\MediaFile;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class StudioFilesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_admin_sees_upload_edit_and_delete_on_files_page(): void
    {
        [$org, $admin, $project] = $this->studioWithProject('studio_admin');
        $file = MediaFile::factory()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'original_name' => 'ceremony.jpg',
            'name' => 'ceremony.jpg',
        ]);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(Index::class)
            ->assertSee('Upload file')
            ->assertSee('ceremony.jpg')
            ->assertSee('Edit')
            ->assertSee('Delete');
    }

    public function test_admin_role_can_upload_but_cannot_delete(): void
    {
        [$org, $admin, $project] = $this->studioWithProject('admin');
        MediaFile::factory()->create([
            'organization_id' => $org->id,
            'project_id' => $project->id,
            'original_name' => 'preview.jpg',
            'name' => 'preview.jpg',
        ]);

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        Livewire::test(Index::class)
            ->assertSee('Upload file')
            ->assertSee('Edit')
            ->assertDontSee('Delete');
    }

    public function test_studio_admin_can_upload_rename_and_delete_a_file(): void
    {
        Storage::fake('local');

        [$org, $admin, $project] = $this->studioWithProject('studio_admin');

        $this->actingAs($admin)->withSession(['current_organization_id' => $org->id]);

        $upload = UploadedFile::fake()->image('couple.jpg', 400, 300)->size(200);

        Livewire::test(Index::class)
            ->set('project_id', $project->id)
            ->set('upload_kind', 'edited')
            ->set('upload', $upload)
            ->call('uploadFile')
            ->assertHasNoErrors();

        $file = MediaFile::query()->first();
        $this->assertNotNull($file);
        $this->assertSame('couple.jpg', $file->original_name);
        Storage::disk('local')->assertExists($file->path);

        Livewire::test(Index::class)
            ->call('startEdit', $file->id)
            ->set('edit_name', 'couple-final.jpg')
            ->set('edit_kind', 'preview')
            ->call('updateFile')
            ->assertHasNoErrors();

        $file->refresh();
        $this->assertSame('couple-final.jpg', $file->original_name);
        $this->assertSame('preview', $file->kind->value);

        Livewire::test(Index::class)
            ->call('delete', $file->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted($file);
        Storage::disk('local')->assertMissing($file->path);
    }

    /**
     * @return array{0: Organization, 1: User, 2: Project}
     */
    private function studioWithProject(string $role): array
    {
        $org = Organization::factory()->create(['is_active' => true]);
        $user = User::factory()->create();
        $org->users()->attach($user->id, ['role' => $role, 'accepted_at' => now()]);
        Tenant::set($org->id);

        $customer = Customer::factory()->create(['organization_id' => $org->id]);
        $project = Project::factory()->create([
            'organization_id' => $org->id,
            'customer_id' => $customer->id,
            'title' => 'Lake Palace Wedding',
        ]);

        return [$org, $user, $project];
    }
}
