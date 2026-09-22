<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $orgId = $request->session()->get('current_organization_id');
        if (! $orgId) {
            $orgId = $user->memberships()->value('organization_id');
            if ($orgId) {
                $request->session()->put('current_organization_id', $orgId);
            }
        }

        if ($orgId && ! $user->memberships()->where('organization_id', $orgId)->exists()) {
            abort(403);
        }

        Tenant::set($orgId ? (int) $orgId : null);

        return $next($request);
    }
}
