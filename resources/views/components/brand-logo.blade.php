@props([
    'class' => 'h-10',
])
<img
    src="{{ studio_logo_src() }}"
    alt="{{ site('brand.name', 'Unik Studio') }}"
    {{ $attributes->merge(['class' => $class.' w-auto object-contain']) }}
>
