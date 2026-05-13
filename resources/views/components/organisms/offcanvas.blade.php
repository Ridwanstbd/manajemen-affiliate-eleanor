@props([
    'id', 
    'title' => null, 
    'position' => 'end',
    'md' => false, 
    'lg' => false, 
])

@php
    $sizeClass = '';
    if ($lg) {
        $sizeClass = 'offcanvas-lg';
    } elseif ($md) {
        $sizeClass = 'offcanvas-md';
    }
@endphp

<x-atoms.offcanvas-overlay target="{{ $id }}" />

<div class="offcanvas offcanvas-{{ $position }} {{ $sizeClass }}" id="{{ $id }}" tabindex="-1">
    @if($title)
        <x-molecules.offcanvas-header :title="$title" target="{{ $id }}" />
    @endif
    
    <x-molecules.offcanvas-body>
        {{ $slot }}
    </x-molecules.offcanvas-body>
</div>