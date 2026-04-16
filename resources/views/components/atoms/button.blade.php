@props([
    'variant' => 'primary', 
    'type' => 'button',
    'size' => 'md',     
    'href' => null,         
])

@php
    $classes = "btn btn-{$variant} btn-{$size}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} style="text-decoration:none;">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif