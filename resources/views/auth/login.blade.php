@extends('layouts.guest')
@php
    $portal = $portal ?? 'client';
    $isStudio = $portal === 'studio';
@endphp
@section('heading', $isStudio ? 'Studio admin sign in' : 'Client sign in')
@section('subheading', $isStudio
    ? 'Access your studio dashboard — leads, projects, galleries, billing and website content.'
    : 'Sign in to view your gallery, timeline, payments and project updates.')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
    @csrf
    <input type="hidden" name="portal" value="{{ $portal }}">
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
                placeholder="{{ $isStudio ? 'admin@yourstudio.com' : 'you@example.com' }}"
                class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-[#0f2744] outline-none ring-[#c4a574]/40 placeholder:text-slate-400 focus:border-[#c4a574] focus:ring-4"
                required
                autofocus
            >
        </div>
        @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="auth-password" class="text-sm font-medium text-[#0f2744]">Password</label>
        <div class="relative mt-1.5">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
            </span>
            <input
                id="auth-password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                placeholder="Your password"
                class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-11 text-sm text-[#0f2744] outline-none ring-[#c4a574]/40 placeholder:text-slate-400 focus:border-[#c4a574] focus:ring-4"
                required
            >
            <button
                type="button"
                class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 transition hover:text-[#9b7b4b]"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
                <svg x-show="!showPassword" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg x-show="showPassword" x-cloak class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3l18 18M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.4M9.9 5.1A11 11 0 0112 5c6.5 0 10 7 10 7a16 16 0 01-3.2 4.1M6.1 6.1A16 16 0 002 12s3.5 7 10 7a11 11 0 003.1-.4"/></svg>
            </button>
        </div>
        @error('password') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-between gap-3 pt-0.5">
        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-[#0f2744]">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-[#c4a574] focus:ring-[#c4a574]">
            Remember me
        </label>
        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#b45309] transition hover:text-[#9b7b4b]">Forgot password</a>
    </div>

    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#c4a574] py-3 text-sm font-semibold text-[#16120f] shadow-sm transition hover:bg-[#b89462]">
        {{ $isStudio ? 'Sign in to studio' : 'Sign in' }}
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>
</form>

<div class="relative my-8 text-center text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
    <span class="relative z-10 bg-white px-3">{{ $isStudio ? 'Not studio staff?' : 'Studio team?' }}</span>
    <span class="absolute inset-x-0 top-1/2 h-px -translate-y-1/2 bg-slate-200"></span>
</div>

@if ($isStudio)
    <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-3 text-sm font-medium text-[#0f2744] transition hover:border-[#c4a574] hover:bg-[#f6f1ea]">
        Client gallery login
    </a>
@else
    <a href="{{ route('studio.login') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#16120f]/15 bg-[#16120f] py-3 text-sm font-medium text-white transition hover:bg-[#2a241f]">
        Studio admin login
    </a>
@endif
@endsection
