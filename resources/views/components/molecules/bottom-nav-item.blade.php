@props(['icon', 'label', 'active' => false, 'href' => '#'])

<a href="{{$href}}" class="nav-item-bottom  {{ $active ? 'active' : '' }}">
    <x-atoms.icon name="{{ $icon }}" class="nav-icon" />
    <span>{{ $label }}</span>
</a>