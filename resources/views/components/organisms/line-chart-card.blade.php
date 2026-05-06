@props([
    'id',
    'title' => 'Grafik Garis',
    'labels' => [],
    'datasets' => [],
    'height' => '250px'
])

<div class="glass-metric-card" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $title }}</h3>
        <span style="font-size: 18px; color: var(--text-secondary); cursor: pointer;">&hellip;</span>
    </div>
    
    <div style="position: relative; height: {{ $height }}; width: 100%;">
        <canvas id="lineChart-{{ $id }}"></canvas>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('lineChart-{{ $id }}').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: {!! json_encode($datasets) !!}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush