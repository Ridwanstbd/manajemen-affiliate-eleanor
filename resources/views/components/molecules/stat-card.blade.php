@props(['color', 'icon', 'trend', 'trendValue', 'value', 'label'])

<div class="stat-card">
    <div class="stat-header">
        <div class="stat-icon-wrap {{ $color }}">
            <x-atoms.icon name="{{ $icon }}" style="width: 20px; height: 20px;" />
        </div>
        <span class="stat-trend {{ $trend === 'up' ? 'trend-up' : 'trend-down' }}">
            <x-atoms.icon name="trend-{{ $trend }}" style="width: 11px; height: 11px;" />
            {{ $trendValue }}
        </span>
    </div>
    
    <x-atoms.typography variant="stat-value">{{ $value }}</x-atoms.typography>
    <x-atoms.typography variant="stat-label">{{ $label }}</x-atoms.typography>
</div>