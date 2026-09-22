<?php

use App\Jobs\SendPaymentReminder;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\PaymentMilestone;
use App\Models\ProjectTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    PaymentMilestone::withoutTenant()
        ->whereIn('status', ['pending', 'due', 'partially_paid'])
        ->whereDate('due_date', '<=', now()->addDay())
        ->each(fn ($milestone) => SendPaymentReminder::dispatch($milestone->id));
})->dailyAt('09:00');

Schedule::call(function () {
    LeadFollowup::withoutTenant()
        ->where('status', 'pending')
        ->where('due_at', '<=', now())
        ->update(['status' => 'due']);

    Lead::withoutTenant()
        ->where('status', 'new')
        ->where('created_at', '<', now()->subDays(3))
        ->update(['follow_up_at' => now()]);

    ProjectTask::withoutTenant()
        ->whereNot('status', 'completed')
        ->where('deadline', '<', now())
        ->update(['priority' => 'urgent']);
})->hourly();
