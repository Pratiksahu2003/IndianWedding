<?php

namespace Tests\Unit;

use App\Enums\Permission;
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

    public function test_studio_admin_has_every_permission_and_can_delete(): void
    {
        foreach (Permission::values() as $permission) {
            $this->assertTrue(Role::StudioAdmin->can($permission), "Studio admin should have {$permission}");
        }

        $this->assertTrue(Role::StudioAdmin->canDelete());
        $this->assertTrue(Role::StudioAdmin->hasFullAccess());
    }

    public function test_admin_has_full_access_but_cannot_delete(): void
    {
        foreach (Permission::values() as $permission) {
            $this->assertTrue(Role::Admin->can($permission), "Admin should have {$permission}");
        }

        $this->assertTrue(Role::Admin->hasFullAccess());
        $this->assertFalse(Role::Admin->canDelete());
    }
}
