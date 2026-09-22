@props([
    'linkClass' => '',
])

@php
    $emails = contact_emails();
@endphp

@if (count($emails) > 0)
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>
        @foreach ($emails as $email)
            <a href="mailto:{{ $email }}" class="{{ $linkClass !== '' ? $linkClass : 'transition hover:text-white' }}">{{ $email }}</a>
        @endforeach
    </div>
@else
    <p {{ $attributes }}>{{ site('contact.emails') }}</p>
@endif
