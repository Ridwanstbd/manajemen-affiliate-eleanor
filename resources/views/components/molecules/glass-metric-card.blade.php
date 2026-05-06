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

<style>
    .glass-metric-card {
        background: var(--glass-bg, rgba(255, 255, 255, 0.6));
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border: 1px solid var(--glass-border, rgba(0,0,0,0.1));
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .metric-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary, #64748b);
        font-weight: 600;
    }
    .metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .metric-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-primary, #0f172a);
        margin-bottom: 4px;
    }
    .metric-subtext {
        font-size: 13px;
        color: var(--text-secondary, #64748b);
    }
</style>