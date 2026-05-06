<x-organisms.grid-layout columns="repeat(4, 1fr)">
    <x-molecules.glass-metric-card title="TOTAL GMV AFILIASI" value="Rp 1.28M" subtext="↗ 12% vs bulan lalu" />
    <x-molecules.glass-metric-card title="BARANG TERJUAL" value="14,205" subtext="↗ 8% vs bulan lalu" />
    <x-molecules.glass-metric-card title="ESTIMASI KOMISI" value="Rp 84.2Jt" subtext="↗ 15% vs bulan lalu" />
    <x-molecules.glass-metric-card title="SAMPEL TERKIRIM" value="450" subtext="↘ 2% vs bulan lalu" trend="down" />
</x-organisms.grid-layout>

<x-organisms.grid-layout columns="2fr 1fr">
    @php
        $trenHarianLabels = ['01', '05', '10', '15', '20', '25', '30'];
        $trenHarianDatasets = [
            ['label' => 'GMV Afiliasi', 'data' => [12, 19, 15, 25, 22, 30, 28], 'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59, 130, 246, 0.1)', 'fill' => true, 'tension' => 0.4],
            ['label' => 'Barang Terjual', 'data' => [5, 8, 12, 10, 15, 18, 16], 'borderColor' => '#10b981', 'backgroundColor' => 'transparent', 'tension' => 0.4]
        ];
    @endphp
    <x-organisms.line-chart-card id="trenHarian" title="Tren Pertumbuhan Harian" :labels="$trenHarianLabels" :datasets="$trenHarianDatasets" />

    @php
        $statusTugasData = [
            ['label' => 'Selesai (Completed)', 'value' => 850, 'color' => '#10b981'],
            ['label' => 'Dalam Proses', 'value' => 400, 'color' => '#f59e0b'],
        ];
    @endphp
    <x-organisms.donut-chart-card title="Status Laporan Tugas" :data="$statusTugasData" />
</x-organisms.grid-layout>

<x-molecules.card  title="Papan Peringkat Affiliator (Top 5)" linkText="Lihat Semua >" linkHref="{{ route('admin-dashboard.leaderboard') }}">
    @php
        $headers = [
            ['label' => 'PERINGKAT'], ['label' => 'USERNAME TIKTOK'], ['label' => 'TOTAL PESANAN'], 
            ['label' => 'VIDEO/LIVE'], ['label' => 'GMV AFILIASI', 'align' => 'right']
        ];
        $top5 = [
            ['#1', '@sarah.beauty', '1,245', '12 / 4', 'Rp 45.2M'],
            ['#2', '@mike.skincare', '980', '8 / 2', 'Rp 38.1M'],
            ['#3', '@emily.glow', '850', '15 / 0', 'Rp 32.9M'],
            ['#4', '@alex.review', '710', '5 / 5', 'Rp 28.4M'],
        ];
    @endphp

    <x-organisms.glass-table :headers="$headers">
        @foreach($top5 as $row)
        <tr>
            <td><x-atoms.badge status="{{ $loop->first ? 'paid' : 'pending' }}">{{ $row[0] }}</x-atoms.badge></td>
            <td style="font-weight: 600; color: var(--text-primary);">{{ $row[1] }}</td>
            <td>{{ $row[2] }}</td>
            <td>{{ $row[3] }}</td>
            <td style="text-align: right; font-weight: 700; color: var(--emerald);">{{ $row[4] }}</td>
        </tr>
        @endforeach
    </x-organisms.glass-table>
</x-molecules.card>