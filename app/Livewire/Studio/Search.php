<?php

namespace App\Livewire\Studio;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MediaFile;
use App\Models\Payment;
use App\Models\Project;
use Livewire\Component;

class Search extends Component
{
    public string $q = '';


    public function render()
    {
        $q = trim($this->q);
        $results = [
            'leads' => $q ? Lead::query()->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('lead_number', 'like', "%{$q}%");
            })->limit(5)->get() : collect(),
            'clients' => $q ? Customer::query()->where('name', 'like', "%{$q}%")->limit(5)->get() : collect(),
            'projects' => $q ? Project::query()->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('project_number', 'like', "%{$q}%");
            })->limit(5)->get() : collect(),
            'invoices' => $q ? Invoice::query()->where('invoice_number', 'like', "%{$q}%")->limit(5)->get() : collect(),
            'payments' => $q ? Payment::query()->where('reference', 'like', "%{$q}%")->limit(5)->get() : collect(),
            'files' => $q ? MediaFile::query()->where('original_name', 'like', "%{$q}%")->limit(5)->get() : collect(),
        ];

        return view('livewire.studio.search', compact('results'));
    }
}
