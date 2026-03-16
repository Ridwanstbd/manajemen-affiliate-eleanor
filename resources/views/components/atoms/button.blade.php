@props([
    'variant' => 'primary', 
    'type' => 'button',     
    'href' => null,         
])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-' . $variant]) }} style="text-decoration:none ;">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-' . $variant]) }}>
        {{ $slot }}
    </button>
@endif