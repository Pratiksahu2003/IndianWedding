@php
    $title = 'Terms & Conditions — '.site('brand.name', 'Unik Studio');
    $description = 'Terms and conditions for booking photography and cinematography services, using our website, and working with our studio.';
@endphp
@extends('layouts.public')
@section('content')
<x-legal-page title="Terms & Conditions" :description="$description" current="terms">
    @include('public.partials.legal.terms-content')
</x-legal-page>
@endsection
