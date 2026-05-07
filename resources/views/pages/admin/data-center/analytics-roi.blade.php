<x-organisms.grid-layout columns="repeat(3, 1fr)">
    <x-molecules.glass-metric-card title="BIAYA SAMPEL" 
        value="Rp {{ number_format($metrics['biaya']['val'], 0, ',', '.') }}" 
        subtext="Bulan ini" 
        trend="{{ $metrics['biaya']['trend'] >= 0 ? 'up' : 'down' }}" 
        trendValue="{{ number_format(abs($metrics['biaya']['trend']), 1) }}%" />
        
    <x-molecules.glass-metric-card title="GMV AFILIASI" 
        value="Rp {{ number_format($metrics['gmv']['val'], 0, ',', '.') }}" 
        trend="{{ $metrics['gmv']['trend'] >= 0 ? 'up' : 'down' }}" 
        trendValue="{{ number_format(abs($metrics['gmv']['trend']), 1) }}%" />
        
    <x-molecules.glass-metric-card title="RASIO ROI" 
        value="{{ number_format($metrics['roi']['val'], 2) }}x" 
        trend="{{ $metrics['roi']['trend'] >= 0 ? 'up' : 'down' }}" 
        trendValue="{{ number_format(abs($metrics['roi']['trend']), 2) }}x" />
</x-organisms.grid-layout>
<div class="dashboard-grid" style="margin-top: 20px;">
    <x-molecules.card title="Analisis Corong Distribusi">
        <x-molecules.funnel-step width="100%" title="Total Permintaan" value="{{ number_format($funnel['total']) }}" percentage="100%" />
        <x-molecules.funnel-step width="80%" title="Disetujui & Dikirim (+ Data Eksternal)" value="{{ number_format($funnel['approved']) }}" percentage="{{ $funnel['approved_pct'] }}%" />
        <x-molecules.funnel-step width="60%" title="Konten Dibuat" value="{{ number_format($funnel['content']) }}" percentage="{{ $funnel['content_pct'] }}%" />
        <x-molecules.funnel-step width="40%" title="Konversi Penjualan" value="{{ number_format($funnel['conversion']) }}" />
    </x-molecules.card>
    
    <x-molecules.card title="Performa Produk">
        <div class="table-responsive" style="overflow-x: auto; margin-top: 10px;">
                <table class="w-100" style="text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 13px;">
                            <th style="padding: 10px 8px; font-weight: 600;">Nama Barang</th>
                            <th style="padding: 10px 8px; font-weight: 600;">Konversi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $tp)
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 14px;">
                            <td style="padding: 12px 8px;">
                                {{ $tp['name'] }}
                            </td>
                            <td style="padding: 12px 8px; color: var(--emerald); font-weight: 600;">
                                {{ $tp['roi'] }} 
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 16px; text-align: center; color: var(--text-tertiary); font-size: 13px;">
                                Belum ada data Produk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        
    </x-molecules.card>
</div>

<x-molecules.card  title="Komparasi Performa Bulanan" style="margin-top: 20px;">
    <x-slot name="headerAction">
        <form method="GET" action="{{ route('admin-dashboard.analytics') }}" style="display: flex; align-items: center; gap: 8px;">
            <input type="hidden" name="tab" value="analytics">
            <label style="font-size: 13px;">Bandingkan:</label>
            <input type="month" name="month_1" value="{{ $month1 }}" onchange="this.form.submit()" style="...">
            <span style="font-size: 13px;">vs</span>
            <input type="month" name="month_2" value="{{ $month2 }}" onchange="this.form.submit()" style="...">
        </form>
    </x-slot>

    <div style="margin-top: -24px; position: relative; height: 400px; width: 100%;">
        <canvas id="komparasiBarChart"></canvas>
    </div>
</x-molecules.card>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartData = @json($komparasiData);

        const labels = chartData.map(item => item.kategori);
        const dataBulan1 = chartData.map(item => item.bulan_1);
        const dataBulan2 = chartData.map(item => item.bulan_2);

        const ctx = document.getElementById('komparasiBarChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Bulan Pertama',
                        data: dataBulan1,
                        backgroundColor: 'rgba(15, 23, 42, 0.15)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Bulan Kedua',
                        data: dataBulan2,
                        backgroundColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>
@endpush