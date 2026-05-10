@props([
    'id', 
    'title' => null, 
    'position' => 'end' 
])

<x-atoms.offcanvas-overlay target="{{ $id }}" />

<div class="offcanvas offcanvas-{{ $position }}" id="{{ $id }}" tabindex="-1">
    @if($title)
        <x-molecules.offcanvas-header :title="$title" target="{{ $id }}" />
    @endif
    
    <x-molecules.offcanvas-body>
        {{ $slot }}
    </x-molecules.offcanvas-body>
</div>