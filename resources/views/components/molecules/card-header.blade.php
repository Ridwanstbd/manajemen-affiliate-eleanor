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

<style>
    .glass-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .glass-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .glass-card-icon {
        width: 20px;
        height: 20px;
        color: var(--text-secondary);
        cursor: pointer;
    }
    .glass-card-link {
        font-size: 13px;
        color: var(--primary-blue, #3b82f6);
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
    }
    .glass-card-link:hover {
        text-decoration: underline;
    }
</style>