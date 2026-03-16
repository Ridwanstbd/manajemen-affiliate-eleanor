@props([
    'variant' => 'body', // default variant
    'as' => null         // override HTML tag jika diperlukan
])

@php
    $defaultTags = [
        'hero-title'     => 'h1',
        'hero-subtitle'  => 'p',
        'card-title'     => 'h3',
        'stat-value'     => 'div',
        'stat-label'     => 'div',
        'nav-label'      => 'div',
        'timeline-title' => 'div',
        'timeline-time'  => 'div',
        'company-name'   => 'span',
        'body'           => 'p',
    ];

    $tag = $as ?? ($defaultTags[$variant] ?? 'span');
    
    $baseClass = $variant === 'body' ? '' : $variant;
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $baseClass]) }}>
    {{ $slot }}
</{{ $tag }}>