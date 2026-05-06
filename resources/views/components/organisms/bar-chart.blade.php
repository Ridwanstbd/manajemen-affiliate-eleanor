@props([
    'title' => 'Grafik Data',
    'data' => [],            
    'labelKey' => 'label',       
    
    'bar1Key' => 'value1',       
    'bar1ValueKey' => null, 
    'bar1Label' => 'Data 1',    
    'bar1Color' => '#4CAF50',
    
    'bar2Key' => null,           
    'bar2ValueKey' => null, 
    'bar2Label' => 'Data 2',  
    'bar2Color' => '#F44336',   
])

<div class="chart-card">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
        
        <div class="chart-legend">
            <div class="legend-item">
                <div class="legend-dot" style="background-color: {{ $bar1Color }}; width: 10px; height: 10px; border-radius: 50%; display: inline-block;"></div>
                <span>{{ $bar1Label }}</span>
            </div>
            
            @if($bar2Key)
            <div class="legend-item">
                <div class="legend-dot" style="background-color: {{ $bar2Color }}; width: 10px; height: 10px; border-radius: 50%; display: inline-block;"></div>
                <span>{{ $bar2Label }}</span>
            </div>
            @endif
        </div>
    </div>
    
    <div class="chart-wrapper">
        <div class="chart-area">
            <div class="chart-container">
                @forelse($data as $item)
                    @php
                        $val1 = $bar1ValueKey ? ($item[$bar1ValueKey] ?? 0) : ($item[$bar1Key] ?? 0);
                        $val2 = $bar2ValueKey ? ($item[$bar2ValueKey] ?? 0) : ($item[$bar2Key] ?? 0);
                    @endphp

                    <div class="chart-bar-group">
                        <div class="chart-bars">
                            <div class="bar bar-with-tooltip" 
                                 data-tooltip="{{ $bar1Label }}: {{ is_numeric($val1) && $val1 > 1000 ? number_format($val1, 0, ',', '.') : $val1 }}"
                                 style="height: {{ $item[$bar1Key] ?? 0 }}%; background-color: {{ $bar1Color }};">
                            </div>
                            
                            @if($bar2Key)
                            <div class="bar bar-with-tooltip" 
                                 data-tooltip="{{ $bar2Label }}: {{ is_numeric($val2) && $val2 > 1000 ? number_format($val2, 0, ',', '.') : $val2 }}"
                                 style="height: {{ $item[$bar2Key] ?? 0 }}%; background-color: {{ $bar2Color }};">
                            </div>
                            @endif
                        </div>
                        <span class="bar-label">{{ $item[$labelKey] ?? '' }}</span>
                    </div>
                @empty
                    <p style="font-size: 12px; color: var(--text-tertiary); text-align: center;">Belum ada data tersedia</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .bar-with-tooltip {
        position: relative;
        cursor: pointer;
        transition: opacity 0.2s ease;
        z-index: 1;
    }
    
    .bar-with-tooltip:hover {
        opacity: 0.8;
        z-index: 99;
    }

    .bar-with-tooltip:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 5px);
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(15, 23, 42, 0.9);
        color: #ffffff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        pointer-events: none;
        z-index: 50;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>