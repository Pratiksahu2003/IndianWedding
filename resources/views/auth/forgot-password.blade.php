@extends('layouts.guest')
@section('content')
<form method="POST" action="{{ route('password.email') }}" class="rounded-3xl bg-[#f6f1ea] p-8 text-[#16120f]">
    @csrf
    <h1 class="font-[Cormorant_Garamond] text-3xl">Reset password</h1>
    <label class="mt-6 block text-sm">Email<input class="mt-1 w-full rounded-2xl px-4 py-3" type="email" name="email" required></label>
    <button class="mt-6 w-full rounded-full bg-[#16120f] py-3 text-white">Send reset link</button>
</form>
@endsection
