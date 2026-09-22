<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        $portal = $request->routeIs('studio.login') ? 'studio' : 'client';

        return view('auth.login', [
            'portal' => $portal,
        ]);
    }

    public function store(LoginRequest $request, AuditLogger $audit): RedirectResponse
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('These credentials do not match our records.')])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user();
        abort_unless($user->is_active, 403, 'This account is disabled.');

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $audit->log('auth.login', $user);

        $membership = $user->memberships()->first();
        if ($membership) {
            $request->session()->put('current_organization_id', $membership->organization_id);
        }

        $role = $user->roleIn($membership?->organization);
        $portal = $request->input('portal', 'client');

        if ($portal === 'studio' && $role === Role::Client) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => __('This email is registered as a client. Please use client login.')])
                ->onlyInput('email');
        }

        return redirect()->intended(route($role?->dashboardRoute() ?? 'app.dashboard'));
    }

    public function destroy(Request $request, AuditLogger $audit): RedirectResponse
    {
        $audit->log('auth.logout', $request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
