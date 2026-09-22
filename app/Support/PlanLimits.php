<?php

namespace App\Support;

use App\Models\Organization;

class PlanLimits
{
    public static function allows(Organization $organization, string $metric, int $current): bool
    {
        $plan = $organization->plan;
        if (! $plan) {
            return true;
        }

        $max = match ($metric) {
            'users' => $plan->max_users,
            'projects' => $plan->max_projects,
            'clients' => $plan->max_clients,
            'storage_mb' => $plan->max_storage_mb,
            'whatsapp' => $plan->max_whatsapp_messages,
            default => null,
        };

        return $max === null || $current < $max;
    }
}
