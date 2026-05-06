<x-organisms.grid-layout columns="repeat(4, 1fr)">
    <x-molecules.glass-metric-card title="GMV DIHASILKAN (SAMPEL)" value="Rp 235.5M" subtext="↑ 14% vs periode lalu" trend="up" />
    <x-molecules.glass-metric-card title="TOTAL BIAYA SAMPEL" value="Rp 45.2M" subtext="↓ 2.4% vs periode lalu" trend="down" />
    <x-molecules.glass-metric-card title="RATA-RATA RASIO ROI" value="5.21x" subtext="↑ 0.8x vs periode lalu" trend="up" />
    <x-molecules.glass-metric-card title="PESANAN TERATRIBUSI" value="12,405" subtext="↑ 5.2% vs periode lalu" trend="up" />
</x-organisms.grid-layout>

<x-organisms.grid-layout columns="1fr 1fr">
    @php
        $sumberKonversiData = [['label' => 'TikTok Video', 'value' => 75, 'color' => '#3b82f6'], ['label' => 'TikTok Live', 'value' => 25, 'color' => '#ec4899']];
        $roiLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'];
        $roiDatasets = [['label' => 'Rasio ROI (x)', 'data' => [3.2, 3.8, 4.5, 4.2, 5.21], 'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139, 92, 246, 0.1)', 'fill' => true, 'tension' => 0.4]];
    @endphp

    <x-organisms.donut-chart-card title="Sumber Konversi (Video vs Live)" :data="$sumberKonversiData" />
    
    <div style="position: relative;">
        <x-organisms.line-chart-card id="trenRoi" title="Tren Rasio ROI Bulanan" :labels="$roiLabels" :datasets="$roiDatasets" />
        <div style="text-align: center; font-size: 11px; color: var(--text-secondary); margin-top: -20px; padding-bottom: 24px; width: 100%;">
            Metrik: Rasio Pengembalian Investasi (GMV / Biaya Sampel)
        </div>
    </div>
</x-organisms.grid-layout>

<x-molecules.card  title="Analisis Detail ROI Per Produk">
    @php
        $headers = [
            ['label' => 'NAMA PRODUK'], ['label' => 'SAMPEL TERKIRIM'], ['label' => 'BIAYA SAMPEL'], 
            ['label' => 'GMV DIHASILKAN'], ['label' => 'RASIO ROI']
        ];
        $products = [
            ['name' => 'Serum Alpha', 'cat' => 'Perawatan Wajah', 'sent' => '150 unit', 'cost' => 'Rp 15,000,000', 'gmv' => 'Rp 126,000,000', 'roi' => '8.4x'],
            ['name' => 'Moisturizer X', 'cat' => 'Perawatan Wajah', 'sent' => '200 unit', 'cost' => 'Rp 10,000,000', 'gmv' => 'Rp 61,000,000', 'roi' => '6.1x'],
            ['name' => 'Cleanser Pro', 'cat' => 'Pembersih', 'sent' => '100 unit', 'cost' => 'Rp 5,000,000', 'gmv' => 'Rp 24,000,000', 'roi' => '4.8x'],
        ];
    @endphp

    <x-organisms.glass-table :headers="$headers">
        @foreach($products as $p)
        <tr>
            <td>
                <div style="font-weight: 700; color: var(--text-primary);">{{ $p['name'] }}</div>
                <div style="font-size: 12px; color: var(--text-secondary);">{{ $p['cat'] }}</div>
            </td>
            <td>{{ $p['sent'] }}</td>
            <td>{{ $p['cost'] }}</td>
            <td>{{ $p['gmv'] }}</td>
            <td><x-atoms.badge status="paid" style="font-size: 14px;">{{ $p['roi'] }}</x-atoms.badge></td>
        </tr>
        @endforeach
    </x-organisms.glass-table>
</x-molecules.card>