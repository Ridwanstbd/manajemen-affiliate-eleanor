@php
    $totalGmv = $products->sum('gmv');
    $totalCost = $products->sum('cost');
    $avgRoi = $totalCost > 0 ? ($totalGmv / $totalCost) : 0;
    $totalOrders = $totalOrders ?? 0; 
@endphp

<x-organisms.grid-layout columns="repeat(4, 1fr)">
    <x-molecules.glass-metric-card 
        title="GMV DIHASILKAN (SAMPEL)" 
        value="Rp {{ number_format($totalGmv, 0, ',', '.') }}" 
        subtext="Berdasarkan performa produk" 
        trend="up" 
    />
    <x-molecules.glass-metric-card 
        title="TOTAL BIAYA SAMPEL" 
        value="Rp {{ number_format($totalCost, 0, ',', '.') }}" 
        subtext="Total biaya pengiriman" 
        trend="down" 
    />
    <x-molecules.glass-metric-card 
        title="RATA-RATA RASIO ROI" 
        value="{{ number_format($avgRoi, 2) }}x" 
        subtext="Total GMV / Total Biaya" 
        trend="up" 
    />
    <x-molecules.glass-metric-card 
        title="PESANAN TERATRIBUSI" 
        value="{{ number_format($totalOrders, 0, ',', '.') }}" 
        subtext="Total pesanan dari sampel" 
        trend="up" 
    />
</x-organisms.grid-layout>

<x-organisms.grid-layout columns="1fr 1fr">
    <x-organisms.donut-chart-card
        title="Sumber Konversi (Video vs Live)"
        :data="$sumberKonversi"
    />

    <div style="position: relative;">
        <x-organisms.line-chart-card
            id="trenRoi"
            title="Tren Rasio ROI Bulanan"
            :labels="['Jan', 'Feb', 'Mar', 'Apr', 'Mei']"
            :datasets="[[
                'label' => 'Rasio ROI (x)',
                'data' => [3.2, 3.8, 4.5, 4.2, number_format($avgRoi, 2)],
                'borderColor' => '#8b5cf6',
                'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                'fill' => true,
                'tension' => 0.4
            ]]"
        />
        <div style="text-align: center; font-size: 11px; color: var(--text-secondary); margin-top: -20px; padding-bottom: 24px; width: 100%;">
            Metrik: Rasio Pengembalian Investasi (GMV / Biaya Sampel)
        </div>
    </div>
</x-organisms.grid-layout>

<x-molecules.card title="Analisis Detail ROI Per Produk">
    @php
        $columns = [
            ['data' => 'name_cat', 'title' => 'NAMA PRODUK'],
            ['data' => 'sent', 'title' => 'SAMPEL TERKIRIM'],
            ['data' => 'cost_formatted', 'title' => 'BIAYA SAMPEL'],
            ['data' => 'gmv_formatted', 'title' => 'GMV DIHASILKAN'],
            ['data' => 'roi_badge', 'title' => 'RASIO ROI']
        ];
    @endphp

    <x-organisms.datatables 
        id="detail-roi-table" 
        url="{{ route('admin-dashboard.analytics.detail-roi-data') }}" 
        :columns="$columns" 
    />
</x-molecules.card>