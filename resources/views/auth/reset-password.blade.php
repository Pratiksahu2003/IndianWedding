@extends('layouts.guest')
@section('content')
<form method="POST" action="{{ route('password.store') }}" class="rounded-3xl bg-[#f6f1ea] p-8 text-[#16120f]">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <label class="block text-sm">Email<input class="mt-1 w-full rounded-2xl px-4 py-3" type="email" name="email" value="{{ old('email', $request->email) }}"></label>
    <label class="mt-4 block text-sm">Password<input class="mt-1 w-full rounded-2xl px-4 py-3" type="password" name="password" required></label>
    <label class="mt-4 block text-sm">Confirm<input class="mt-1 w-full rounded-2xl px-4 py-3" type="password" name="password_confirmation" required></label>
    <button class="mt-6 w-full rounded-full bg-[#16120f] py-3 text-white">Reset</button>
</form>
@endsection
