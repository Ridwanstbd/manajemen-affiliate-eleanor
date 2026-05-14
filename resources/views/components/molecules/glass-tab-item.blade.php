@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" class="glass-tab-item {{ $active ? 'active' : '' }}">
    {{ $slot }}
</a>
