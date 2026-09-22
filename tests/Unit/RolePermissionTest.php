<?php

namespace Tests\Unit;

use App\Enums\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    public function test_client_cannot_manage_billing(): void
    {
        $this->assertFalse(Role::Client->can('billing.manage'));
        $this->assertTrue(Role::StudioAdmin->can('billing.manage'));
        $this->assertTrue(Role::Photographer->can('files.upload'));
        $this->assertFalse(Role::Photographer->can('users.manage'));
    }
}
