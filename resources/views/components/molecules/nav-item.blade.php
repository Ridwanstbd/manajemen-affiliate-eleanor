@props(['icon', 'label', 'active' => false, 'href' => '#', 'badge' => null])

<a href="{{ $href }}" class="nav-item {{ $active ? 'active' : '' }}" style="display: flex; align-items: center; padding-right: 16px;" {{ $attributes }}>
    <x-atoms.icon name="{{ $icon }}" class="nav-icon" />
    
    <span style="flex-grow: 1;">{{ $label }}</span>
    
    @if($badge)
        <span style="position: relative; background-color: #ef4444; color: white; padding: 0 6px; border-radius: 12px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; flex-shrink: 0; line-height: 1;">
            {{ $badge }}
        </span>
    @endif
</a>