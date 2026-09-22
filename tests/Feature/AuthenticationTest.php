<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_visible(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Wedding, pre-wedding')
            ->assertSee('/Logo/', false);
    }

    public function test_user_can_login(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['email' => 'a@example.com']);
        $org->users()->attach($user->id, ['role' => 'studio_admin', 'accepted_at' => now()]);

        $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }
}
