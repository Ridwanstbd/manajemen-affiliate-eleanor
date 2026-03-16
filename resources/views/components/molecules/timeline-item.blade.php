@props(['type', 'icon', 'title', 'amount', 'amountType', 'status', 'statusClass', 'time'])

<div class="timeline-item">
    <div class="timeline-icon {{ $type }}">
        <x-atoms.icon name="{{ $icon }}" style="width: 16px; height: 16px;" />
    </div>
    <div class="timeline-content">
        <x-atoms.typography variant="timeline-title">{{ $title }}</x-atoms.typography>
        
        <div class="timeline-meta">
            <x-atoms.typography variant="timeline-amount" class="{{ $amountType }}">
                {{ $amount }}
            </x-atoms.typography>
            
            <x-atoms.badge status="{{ strtolower($status) }}">
                {{ $status }}
            </x-atoms.badge>
        </div>
        
        <x-atoms.typography variant="timeline-time">{{ $time }}</x-atoms.typography>
    </div>
</div>