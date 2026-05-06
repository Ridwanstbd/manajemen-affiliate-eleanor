@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" class="glass-tab-item {{ $active ? 'active' : '' }}">
    {{ $slot }}
</a>

<style>
    .glass-tab-item {
        font-size: 16px;
        font-weight: 500;
        color: var(--text-secondary, #64748b);
        text-decoration: none;
        padding-bottom: 10px;
        position: relative;
        transition: color 0.2s ease;
    }
    .glass-tab-item:hover {
        color: var(--text-primary, #0f172a);
    }
    .glass-tab-item.active {
        color: var(--text-primary, #0f172a);
        font-weight: 700;
    }
    .glass-tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -9px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--text-primary, #0f172a);
        border-radius: 3px 3px 0 0;
    }
</style>