@props(['icon', 'label', 'active' => false, 'href' => '#', 'badge' => null])

<a href="{{ $href }}" class="nav-item {{ $active ? 'active' : '' }}" {{ $attributes }}>
    <x-atoms.icon name="{{ $icon }}" class="nav-icon" />
    <span style="flex-grow: 1;">{{ $label }}</span>
    
    @if($badge)
        <span class="badge" style="background-color: #ef4444; color: white; padding: 2px 6px; border-radius: 12px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px;">
            {{ $badge }}
        </span>
    @endif
</a>