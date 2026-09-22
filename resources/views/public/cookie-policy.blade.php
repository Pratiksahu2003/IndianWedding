@php
    $title = 'Cookie Policy — '.site('brand.name', 'Unik Studio');
    $description = 'Learn which cookies and similar tools we use on our website, what they do, and how you can control them in your browser or device settings.';
@endphp
@extends('layouts.public')
@section('content')
<x-legal-page title="Cookie Policy" :description="$description" current="cookies">
    @include('public.partials.legal.cookies-content')
</x-legal-page>
@endsection
