<?php

namespace App\Enums;

enum Role: string
{
    case StudioAdmin = 'studio_admin';
    case Manager = 'manager';
    case Photographer = 'photographer';
    case Videographer = 'videographer';
    case Editor = 'editor';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::StudioAdmin => 'Admin',
            self::Manager => 'Manager',
            self::Photographer => 'Photographer',
            self::Videographer => 'Videographer',
            self::Editor => 'Editor',
            self::Client => 'Client',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::StudioAdmin => [
                'billing.manage', 'users.manage', 'settings.manage', 'leads.manage',
                'projects.manage', 'payments.manage', 'files.manage', 'reports.view',
                'packages.manage', 'consultations.manage', 'team.assign', 'galleries.manage',
                'invoices.manage', 'clients.manage', 'messages.manage',
            ],
            self::Manager => [
                'leads.manage', 'projects.manage', 'payments.manage', 'reports.view',
                'clients.manage', 'team.assign', 'consultations.manage', 'invoices.manage',
                'files.manage', 'galleries.manage', 'messages.manage', 'packages.view',
            ],
            self::Photographer => [
                'projects.assigned', 'files.upload', 'tasks.assigned', 'calendar.view', 'messages.manage',
            ],
            self::Videographer => [
                'projects.assigned', 'files.upload', 'tasks.assigned', 'calendar.view', 'messages.manage',
            ],
            self::Editor => [
                'projects.assigned', 'files.raw', 'files.upload', 'tasks.assigned', 'messages.manage',
            ],
            self::Client => [
                'portal.view', 'gallery.view', 'payments.own', 'invoices.own', 'files.download',
            ],
        };
    }

    public function can(string $permission): bool
    {
        $perms = $this->permissions();

        return in_array($permission, $perms, true);
    }

    public function isStaff(): bool
    {
        return $this !== self::Client;
    }

    public function dashboardRoute(): string
    {
        return $this === self::Client ? 'client.dashboard' : 'app.dashboard';
    }
}
