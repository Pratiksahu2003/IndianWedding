@extends('layouts.guest')
@section('heading', 'Reset password')
@section('subheading', 'Enter your email and we will send a reset link if an account exists.')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    @if (session('status'))
        <p class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif
    <div>
        <label for="auth-email" class="text-sm font-medium text-[#0f2744]">Email</label>
        <div class="relative mt-1.5">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/></svg>
            </span>
            <input
                id="auth-email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                placeholder="you@example.com"
                class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm outline-none ring-[#c4a574]/40 placeholder:text-slate-400 focus:border-[#c4a574] focus:ring-4"
                required
                autofocus
            >
        </div>
        @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#c4a574] py-3 text-sm font-semibold text-[#16120f] hover:bg-[#b89462]">
        Send reset link
    </button>
    <p class="text-center text-sm text-slate-500">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-[#b45309]">Sign in</a>
    </p>
</form>
@endsection
