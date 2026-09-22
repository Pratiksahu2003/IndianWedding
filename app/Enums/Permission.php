<?php

namespace App\Enums;

enum Permission: string
{
    case BillingManage = 'billing.manage';
    case UsersManage = 'users.manage';
    case PermissionsManage = 'permissions.manage';
    case SettingsManage = 'settings.manage';
    case LeadsManage = 'leads.manage';
    case ProjectsManage = 'projects.manage';
    case ProjectsAssigned = 'projects.assigned';
    case PaymentsManage = 'payments.manage';
    case PaymentsOwn = 'payments.own';
    case FilesManage = 'files.manage';
    case FilesUpload = 'files.upload';
    case FilesRaw = 'files.raw';
    case FilesDownload = 'files.download';
    case ReportsView = 'reports.view';
    case PackagesManage = 'packages.manage';
    case PackagesView = 'packages.view';
    case ConsultationsManage = 'consultations.manage';
    case TeamAssign = 'team.assign';
    case GalleriesManage = 'galleries.manage';
    case InvoicesManage = 'invoices.manage';
    case InvoicesOwn = 'invoices.own';
    case ClientsManage = 'clients.manage';
    case CalendarManage = 'calendar.manage';
    case CalendarView = 'calendar.view';
    case TasksAssigned = 'tasks.assigned';
    case PortalView = 'portal.view';
    case GalleryView = 'gallery.view';

    public function label(): string
    {
        return match ($this) {
            self::BillingManage => 'Billing',
            self::UsersManage => 'Manage users',
            self::PermissionsManage => 'Manage permissions',
            self::SettingsManage => 'Studio settings',
            self::LeadsManage => 'Leads & pipeline',
            self::ProjectsManage => 'All projects',
            self::ProjectsAssigned => 'Assigned projects',
            self::PaymentsManage => 'Payments',
            self::PaymentsOwn => 'Own payments',
            self::FilesManage => 'All files',
            self::FilesUpload => 'Upload files',
            self::FilesRaw => 'Raw files',
            self::FilesDownload => 'Download files',
            self::ReportsView => 'Reports',
            self::PackagesManage => 'Manage packages',
            self::PackagesView => 'View packages',
            self::ConsultationsManage => 'Consultations',
            self::TeamAssign => 'Assign team',
            self::GalleriesManage => 'Galleries',
            self::InvoicesManage => 'Invoices',
            self::InvoicesOwn => 'Own invoices',
            self::ClientsManage => 'Clients',
            self::CalendarManage => 'Calendar (full)',
            self::CalendarView => 'Calendar (view)',
            self::TasksAssigned => 'Assigned tasks',
            self::PortalView => 'Client portal',
            self::GalleryView => 'View gallery',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::BillingManage, self::PaymentsManage, self::PaymentsOwn, self::InvoicesManage, self::InvoicesOwn => 'Finance',
            self::LeadsManage, self::ClientsManage, self::ConsultationsManage => 'Pipeline',
            self::ProjectsManage, self::ProjectsAssigned, self::TeamAssign, self::TasksAssigned, self::CalendarManage, self::CalendarView => 'Production',
            self::FilesManage, self::FilesUpload, self::FilesRaw, self::FilesDownload, self::GalleriesManage, self::GalleryView => 'Delivery',
            self::PackagesManage, self::PackagesView => 'Packages',
            self::UsersManage, self::PermissionsManage, self::SettingsManage, self::ReportsView => 'Studio',
            self::PortalView => 'Client',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, list<self>>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::cases() as $permission) {
            if ($permission === self::PortalView || $permission === self::GalleryView || $permission === self::PaymentsOwn || $permission === self::InvoicesOwn) {
                continue;
            }

            $groups[$permission->group()][] = $permission;
        }

        return $groups;
    }

    public static function tryFromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
