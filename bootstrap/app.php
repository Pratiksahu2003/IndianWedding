<?php

use App\Enums\Role;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureStudioStaff;
use App\Http\Middleware\SetCurrentOrganization;
use App\Support\Tenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => SetCurrentOrganization::class,
            'role' => EnsureRole::class,
            'permission' => EnsurePermission::class,
            'studio.staff' => EnsureStudioStaff::class,
        ]);
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return '/app';
            }

            if (! Tenant::id()) {
                Tenant::set(Tenant::soleOrganizationId());
            }

            return $user->roleIn(Tenant::current()) === Role::Client ? '/client' : '/app';
        });
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
