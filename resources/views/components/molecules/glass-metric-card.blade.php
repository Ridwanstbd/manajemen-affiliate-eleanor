@props([
    'title' => '',
    'value' => '',
    'subtext' => '',
    'trend' => 'up',
    'trendValue' => ''
])

<div class="glass-metric-card">
    <div class="metric-header">
        <span class="metric-title">{{ $title }}</span>
        @if($trendValue)
        <span class="metric-badge {{ $trend === 'up' ? 'trend-up' : 'trend-down' }}">
            @if($trend === 'up')
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            @else
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            @endif
            {{ $trendValue }}
        </span>
        @endif
    </div>
    <div class="metric-value">{{ $value }}</div>
    <div class="metric-subtext">{{ $subtext }}</div>
</div>