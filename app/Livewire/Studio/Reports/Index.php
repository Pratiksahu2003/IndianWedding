<?php

namespace App\Livewire\Studio\Reports;

use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Reports')]
class Index extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->canInOrganization('reports.view'), 403);
    }

    public function exportLeads()
    {
        abort_unless(auth()->user()?->canInOrganization('reports.view'), 403);

        $rows = Lead::query()->get(['lead_number', 'name', 'email', 'phone', 'status', 'city', 'wedding_date']);
        $csv = "Number,Name,Email,Phone,Status,City,Wedding Date\n";
        foreach ($rows as $row) {
            $csv .= implode(',', [$row->lead_number, $row->name, $row->email, $row->phone, $row->status->value, $row->city, optional($row->wedding_date)?->toDateString()])."\n";
        }

        return response()->streamDownload(fn () => print($csv), 'leads.csv');
    }

    public function render()
    {
        return view('livewire.studio.reports.index', [
            'leadTotal' => Lead::query()->count(),
            'converted' => Lead::query()->where('status', LeadStatus::Booked)->count(),
            'revenue' => Payment::query()->where('status', PaymentStatus::Paid)->sum('amount'),
            'projects' => Project::query()->count(),
        ]);
    }
}
