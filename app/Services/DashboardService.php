<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\PaymentMilestone;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Support\Tenant;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function studio(): array
    {
        $org = Tenant::id();
        $key = 'dash.studio.'.$org.'.'.now()->format('YmdH');

        return Cache::remember($key, 120, function () {
            $leads = Lead::query();
            $projects = Project::query();
            $totalLeads = (clone $leads)->count();
            $newLeads = (clone $leads)->where('status', LeadStatus::New)->count();
            $booked = (clone $leads)->where('status', LeadStatus::Booked)->count();

            $revenue = Payment::query()->where('status', PaymentStatus::Paid)->sum('amount');
            $monthRevenue = Payment::query()->where('status', PaymentStatus::Paid)->whereMonth('paid_at', now()->month)->sum('amount');

            return [
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'conversion' => $totalLeads ? round(($booked / $totalLeads) * 100, 1) : 0,
                'active_projects' => (clone $projects)->whereNot('status', ProjectStatus::Completed)->count(),
                'upcoming_weddings' => (clone $projects)->whereNotNull('wedding_date')->where('wedding_date', '>=', now())->count(),
                'pending_payments' => PaymentMilestone::query()->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Due, PaymentStatus::PartiallyPaid])->sum('amount'),
                'overdue_payments' => PaymentMilestone::query()->where('status', PaymentStatus::Overdue)->orWhere(function ($q) {
                    $q->whereIn('status', [PaymentStatus::Due, PaymentStatus::Pending])->whereDate('due_date', '<', now());
                })->count(),
                'revenue' => $revenue,
                'monthly_revenue' => $monthRevenue,
                'projects_by_status' => Project::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
                'lead_sources' => Lead::query()->with('source')->get()->groupBy(fn ($lead) => $lead->source?->name ?? 'Direct')->map->count(),
                'recent_activity' => ActivityLog::query()->where('organization_id', Tenant::id())->latest()->limit(8)->get(),
            ];
        });
    }
}
