@props([
    'title' => '',
    'value' => '',
    'percentage' => null, 
    'width' => '100%'
])

<div class="funnel-step-wrapper">
    <div class="funnel-bar" style="width: {{ $width }};">
        <span class="funnel-title">{{ $title }}</span>
        <span class="funnel-value">{{ $value }}</span>
    </div>
    
    @if($percentage)
        <div class="funnel-connector">
            <div class="funnel-percentage">{{ $percentage }}</div>
        </div>
    @endif
</div>

<style>
    .funnel-step-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .funnel-bar {
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid var(--glass-border, rgba(0,0,0,0.15));
        border-radius: 4px;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }
    .funnel-bar:hover {
        background: rgba(255, 255, 255, 0.7);
    }
    .funnel-title {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 500;
    }
    .funnel-value {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
    }
    .funnel-connector {
        height: 40px;
        border-left: 1px dashed var(--glass-border, rgba(0,0,0,0.2));
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .funnel-percentage {
        background: var(--glass-bg, rgba(255, 255, 255, 0.8));
        border: 1px solid var(--glass-border, rgba(0,0,0,0.1));
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        transform: translateX(-50%);
    }
</style>