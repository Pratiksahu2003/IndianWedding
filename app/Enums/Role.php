<?php

namespace App\Enums;

enum Role: string
{
    case StudioAdmin = 'studio_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Photographer = 'photographer';
    case Videographer = 'videographer';
    case Editor = 'editor';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::StudioAdmin => 'Studio Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Photographer => 'Photographer',
            self::Videographer => 'Videographer',
            self::Editor => 'Editor',
            self::Client => 'Client',
        };
    }

    public function permissions(): array
    {
        // Studio Admin + Admin: every permission (delete is gated separately).
        if ($this === self::StudioAdmin || $this === self::Admin) {
            return Permission::values();
        }

        return match ($this) {
            self::Manager => [
                'leads.manage', 'projects.manage', 'payments.manage', 'reports.view',
                'clients.manage', 'team.assign', 'consultations.manage', 'invoices.manage',
                'files.manage', 'galleries.manage', 'messages.manage', 'packages.view', 'calendar.manage',
            ],
            self::Photographer => [
                'projects.assigned', 'files.upload', 'tasks.assigned', 'calendar.view', 'calendar.manage', 'messages.manage',
            ],
            self::Videographer => [
                'projects.assigned', 'files.upload', 'tasks.assigned', 'calendar.view', 'calendar.manage', 'messages.manage',
            ],
            self::Editor => [
                'projects.assigned', 'files.raw', 'files.upload', 'tasks.assigned', 'calendar.manage', 'messages.manage',
            ],
            self::Client => [
                'portal.view', 'gallery.view', 'payments.own', 'invoices.own', 'files.download',
            ],
        };
    }

    public function can(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * Only Studio Admin may permanently delete records.
     * Admin can create/update/view everything but cannot delete.
     */
    public function canDelete(): bool
    {
        return $this === self::StudioAdmin;
    }

    public function hasFullAccess(): bool
    {
        return $this === self::StudioAdmin || $this === self::Admin;
    }

    public function isStaff(): bool
    {
        return $this !== self::Client;
    }

    public function dashboardRoute(): string
    {
        return $this === self::Client ? 'client.dashboard' : 'app.dashboard';
    }

    /**
     * @return list<self>
     */
    public static function staffRoles(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $role) => $role->isStaff(),
        ));
    }
}
