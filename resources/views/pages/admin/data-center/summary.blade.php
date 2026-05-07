<x-organisms.grid-layout columns="repeat(4, 1fr)">
    <x-molecules.glass-metric-card title="TOTAL GMV AFILIASI" value="Rp {{ number_format($metrics['gmv']['val'], 0, ',', '.') }}" trend="{{ $metrics['gmv']['trend'] >= 0 ? 'up' : 'down' }}" trendValue="{{ number_format(abs($metrics['gmv']['trend']), 1) }}%" />
    <x-molecules.glass-metric-card title="BARANG TERJUAL" value="{{ number_format($metrics['items']['val'], 0, ',', '.') }}" trend="{{ $metrics['items']['trend'] >= 0 ? 'up' : 'down' }}" trendValue="{{ number_format(abs($metrics['items']['trend']), 1) }}%" />
    <x-molecules.glass-metric-card title="ESTIMASI KOMISI" value="Rp {{ number_format($metrics['komisi']['val'], 0, ',', '.') }}" trend="{{ $metrics['komisi']['trend'] >= 0 ? 'up' : 'down' }}" trendValue="{{ number_format(abs($metrics['komisi']['trend']), 1) }}%" />
    <x-molecules.glass-metric-card title="SAMPEL TERKIRIM" value="{{ number_format($metrics['sampel']['val'], 0, ',', '.') }}" trend="{{ $metrics['sampel']['trend'] >= 0 ? 'up' : 'down' }}" trendValue="{{ number_format(abs($metrics['sampel']['trend']), 1) }}%" />
</x-organisms.grid-layout>

<x-organisms.grid-layout columns="2fr 1fr">
    @php
        $trenHarianDatasets = [
            ['label' => 'GMV Afiliasi', 'data' => $trenHarian['gmv'], 'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59, 130, 246, 0.1)', 'fill' => true, 'tension' => 0.4],
            ['label' => 'Barang Terjual', 'data' => $trenHarian['items'], 'borderColor' => '#10b981', 'backgroundColor' => 'transparent', 'tension' => 0.4]
        ];
    @endphp
    
    <x-organisms.line-chart-card id="trenHarian" title="Tren Pertumbuhan Harian" :labels="$trenHarian['labels']" :datasets="$trenHarianDatasets" />

    <x-organisms.donut-chart-card title="Status Laporan Tugas" :data="$statusTugas" />
</x-organisms.grid-layout>

<x-molecules.card class="glass-metric-card" title="Papan Peringkat Affiliator (Top 5)" linkText="Lihat Semua >" linkHref="{{ route('admin-dashboard.leaderboard') }}">
    
    @php
        $headers = [
            ['label' => 'PERINGKAT'], 
            ['label' => 'USERNAME TIKTOK'], 
            ['label' => 'TOTAL PESANAN'], 
            ['label' => 'GMV AFILIASI', 'align' => 'right']
        ];
    @endphp

    <x-organisms.glass-table :headers="$headers">
        @forelse($top5 as $index => $creator)
        <tr>
            <td>
                <x-atoms.badge status="{{ $index === 0 ? 'paid' : 'pending' }}">
                    #{{ $index + 1 }}
                </x-atoms.badge>
            </td>
            <td style="font-weight: 600; color: var(--text-primary);">
                {{ $creator->user->username ?? 'Unknown' }}
            </td>
            <td>
                {{ number_format($creator->items, 0, ',', '.') }}
            </td>
            <td style="text-align: right; font-weight: 700; color: var(--emerald);">
                Rp {{ number_format($creator->gmv, 0, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center; color: var(--text-tertiary); font-style: italic; padding: 24px;">
                Belum ada data Afiliasi saat ini.
            </td>
        </tr>
        @endforelse
    </x-organisms.glass-table>

</x-molecules.card>