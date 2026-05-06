@props([
    'title' => 'Donut Chart',
    'data' => []
])

<div class="chart-card">
    <div class="card-header" style="margin-bottom: 16px;">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    
    <div style="position: relative; height: 220px; display: flex; justify-content: center; align-items: center;">
        <canvas id="donutChart-{{ Str::slug($title) }}"></canvas>
    </div>

    <div class="custom-legend" style="margin-top: 24px; display: flex; flex-direction: column; gap: 12px;">
        @foreach($data as $item)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: rgba(255, 255, 255, 0.4); border-radius: var(--radius-lg); border: 1px solid rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background-color: {{ $item['color'] }}; width: 12px; height: 12px; border-radius: 4px; box-shadow: 0 2px 6px {{ str_replace('var', 'rgba', $item['color']) }};"></div>
                <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">{{ $item['label'] }}</span>
            </div>
            <span style="font-size: 14px; font-weight: 800; color: var(--text-primary);">{{ $item['value'] }}</span>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('donutChart-{{ Str::slug($title) }}').getContext('2d');
        
        const getCssVariable = (varName) => {
            if(varName.startsWith('var(')) {
                const name = varName.match(/var\(([^)]+)\)/)[1];
                return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
            }
            return varName;
        };

        const rawColors = {!! json_encode(array_column($data, 'color')) !!};
        const resolvedColors = rawColors.map(color => getCssVariable(color));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_column($data, 'label')) !!},
                datasets: [{
                    data: {!! json_encode(array_column($data, 'value')) !!},
                    backgroundColor: resolvedColors,
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', 
                plugins: {
                    legend: { 
                        display: false 
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.85)',
                        titleColor: '#0f172a',
                        bodyColor: '#475569',
                        bodyFont: { weight: 'bold' },
                        borderColor: 'rgba(255, 255, 255, 0.4)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            labelTextColor: function(context) {
                                return '#475569';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush