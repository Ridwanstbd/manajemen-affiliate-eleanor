@props(['title', 'icon' => null, 'linkText' => null, 'linkHref' => '#'])

<div class="glass-card-header">
    <h3 class="glass-card-title">{{ $title }}</h3>

    @if($icon)
        <x-atoms.icon name="{{ $icon }}" class="glass-card-icon" />
    @elseif($linkText)
        <a href="{{ $linkHref }}" class="glass-card-link">{{ $linkText }}</a>
    @else
        {{ $slot }}
    @endif
</div>
