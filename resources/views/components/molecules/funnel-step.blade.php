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
