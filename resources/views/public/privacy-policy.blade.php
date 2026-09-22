@php
    $title = 'Privacy Policy — '.site('brand.name', 'Unik Studio');
    $description = 'Your privacy matters. This policy explains what data we process, why we need it, and the choices available to you when you use our website or book our services.';
@endphp
@extends('layouts.public')
@section('content')
<x-legal-page title="Privacy Policy" :description="$description" current="privacy">
    @include('public.partials.legal.privacy-content')
</x-legal-page>
@endsection
