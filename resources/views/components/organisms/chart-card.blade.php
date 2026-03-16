@props([
    'title' => 'Cash Flow Trend',
    'data' => [] 
])

<div class="chart-card">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
        <div class="chart-legend">
            <div class="legend-item">
                <div class="legend-dot income"></div>
                <span>Income</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot expense"></div>
                <span>Expense</span>
            </div>
        </div>
    </div>
    <div class="chart-wrapper">
        <div class="chart-area">
            <div class="chart-container">
                @forelse($data as $item)
                    <div class="chart-bar-group">
                        <div class="chart-bars">
                            {{-- Mengambil nilai persentase dari array --}}
                            <div class="bar bar-income" style="height: {{ $item['income'] ?? 0 }}%"></div>
                            <div class="bar bar-expense" style="height: {{ $item['expense'] ?? 0 }}%"></div>
                        </div>
                        <span class="bar-label">{{ $item['label'] ?? '' }}</span>
                    </div>
                @empty
                    <p style="font-size: 12px; color: var(--text-tertiary);">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>