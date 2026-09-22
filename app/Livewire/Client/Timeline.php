<?php

namespace App\Livewire\Client;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Timeline')]
class Timeline extends Component
{
    public function render()
    {
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();
        $project = $customer->projects()->with('milestones')->latest()->first();

        return view('livewire.client.timeline', compact('project'));
    }
}
