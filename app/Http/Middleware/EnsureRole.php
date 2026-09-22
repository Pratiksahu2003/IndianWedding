<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        abort_unless($user, 401);

        if ($user->is_super_admin) {
            return $next($request);
        }

        $role = $user->roleIn(Tenant::current());
        abort_unless($role && in_array($role->value, $roles, true), 403);

        return $next($request);
    }
}
