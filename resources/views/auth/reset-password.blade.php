@extends('layouts.guest')
@section('heading', 'Choose a new password')
@section('subheading', 'Use at least 10 characters. You will sign in with this password next.')

@section('content')
<form method="POST" action="{{ route('password.store') }}" class="space-y-4" x-data="{ showPassword: false }">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <div>
        <label for="auth-email" class="text-sm font-medium text-[#0f2744]">Email</label>
        <input
            id="auth-email"
            type="email"
            name="email"
            value="{{ old('email', $request->email) }}"
            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#c4a574] focus:ring-4 focus:ring-[#c4a574]/40"
            required
        >
        @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="auth-password" class="text-sm font-medium text-[#0f2744]">Password</label>
        <div class="relative mt-1.5">
            <input
                id="auth-password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-11 text-sm outline-none focus:border-[#c4a574] focus:ring-4 focus:ring-[#c4a574]/40"
                required
            >
            <button type="button" class="absolute inset-y-0 right-0 px-3.5 text-slate-400" @click="showPassword = !showPassword">Show</button>
        </div>
        @error('password') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="auth-password-confirm" class="text-sm font-medium text-[#0f2744]">Confirm</label>
        <input
            id="auth-password-confirm"
            type="password"
            name="password_confirmation"
            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#c4a574] focus:ring-4 focus:ring-[#c4a574]/40"
            required
        >
    </div>
    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#c4a574] py-3 text-sm font-semibold text-[#16120f] hover:bg-[#b89462]">
        Reset password
    </button>
</form>
@endsection
