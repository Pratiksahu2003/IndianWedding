@extends('layouts.guest')
@section('content')
<form method="POST" action="{{ route('login') }}" class="rounded-3xl bg-[#f6f1ea] p-8 text-[#16120f]">
    @csrf
    <h1 class="font-[Cormorant_Garamond] text-3xl">Welcome back</h1>
    @if (session('status')) <p class="mt-3 text-sm">{{ session('status') }}</p> @endif
    <label class="mt-6 block text-sm">Email<input class="mt-1 w-full rounded-2xl px-4 py-3" type="email" name="email" value="{{ old('email') }}" required></label>
    <label class="mt-4 block text-sm">Password<input class="mt-1 w-full rounded-2xl px-4 py-3" type="password" name="password" required></label>
    <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
    @error('email') <p class="mt-3 text-sm text-rose-700">{{ $message }}</p> @enderror
    <button class="mt-6 w-full rounded-full bg-[#16120f] py-3 text-white">Sign in</button>
    <a href="{{ route('password.request') }}" class="mt-4 block text-center text-sm opacity-60">Forgot password</a>
</form>
@endsection
