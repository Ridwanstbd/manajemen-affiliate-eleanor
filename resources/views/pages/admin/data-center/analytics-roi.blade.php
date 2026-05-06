<x-organisms.grid-layout columns="repeat(3, 1fr)">
    <x-molecules.glass-metric-card title="BIAYA SAMPEL" value="Rp 45.2M" subtext="vs Rp 46.3M bulan lalu" trend="down" trendValue="2.4%" />
    <x-molecules.glass-metric-card title="GMV AFILIASI" value="Rp 235.5M" subtext="vs Rp 208.7M bulan lalu" trend="up" trendValue="12.8%" />
    <x-molecules.glass-metric-card title="RASIO ROI" value="5.2x" subtext="Target: 4.5x" trend="up" trendValue="0.8x" />
</x-organisms.grid-layout>

<x-organisms.grid-layout columns="2fr 1fr">
    
    <x-molecules.card  title="Analisis Corong Distribusi" icon="more-vertical">
        <div style="padding: 0 20px;">
            <x-molecules.funnel-step width="100%" title="Total Permintaan" value="1,250" percentage="80%" />
            <x-molecules.funnel-step width="80%" title="Disetujui & Dikirim" value="1,000" percentage="65%" />
            <x-molecules.funnel-step width="60%" title="Konten Dibuat" value="650" percentage="42%" />
            <x-molecules.funnel-step width="40%" title="Konversi Penjualan" value="273" />
        </div>
    </x-molecules.card>

    <x-organisms.grid-layout columns="1fr" gap="20px" marginBottom="0">
        
        <x-molecules.card  title="Performa Produk" icon="more-vertical" style="flex-grow: 1;">
            <div style="height: 120px; border-left: 1px solid rgba(0,0,0,0.1); border-bottom: 1px solid rgba(0,0,0,0.1); position: relative; display: flex; align-items: flex-end; justify-content: space-around; padding-bottom: 10px;">
                <span style="position: absolute; left: -30px; top: 40%; transform: rotate(-90deg); font-size: 10px; color: var(--text-secondary);">GMV</span>
                <span style="position: absolute; bottom: -20px; font-size: 10px; color: var(--text-secondary);">Vol. Sampel</span>
                
                <div style="width: 30px; height: 30px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); border: 1px solid #3b82f6; display:flex; align-items:center; justify-content:center; font-size: 10px; color: #3b82f6; font-weight: bold;">P1</div>
                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; margin-bottom: 30px; display:flex; align-items:center; justify-content:center; font-size: 10px; color: #10b981; font-weight: bold;">P2</div>
                <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(245, 158, 11, 0.2); border: 1px solid #f59e0b; margin-bottom: 60px; display:flex; align-items:center; justify-content:center; font-size: 10px; color: #f59e0b; font-weight: bold;">P3</div>
            </div>
        </x-molecules.card>

        <x-molecules.card  title="Produk ROI Tertinggi">
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase;">
                <span>Produk</span><span>ROI</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <span style="font-size: 14px; font-weight: 500;">[P3] Serum Alpha</span>
                <span style="font-size: 15px; font-weight: 800; color: var(--emerald);">8.4x</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 14px; font-weight: 500;">[P2] Moisturizer X</span>
                <span style="font-size: 15px; font-weight: 800; color: var(--primary-blue);">6.1x</span>
            </div>
        </x-molecules.card>

    </x-organisms.grid-layout>
</x-organisms.grid-layout>

@php
    $komparasiData = [
        ['kategori' => 'GMV Afiliasi', 'bulan_lalu' => 70, 'bulan_ini' => 95],
        ['kategori' => 'Aktivitas Kreator', 'bulan_lalu' => 50, 'bulan_ini' => 65],
        ['kategori' => 'Tk. Pengembalian', 'bulan_lalu' => 40, 'bulan_ini' => 30],
    ];
@endphp

<x-organisms.bar-chart 
    title="Komparasi Performa Bulanan" 
    :data="$komparasiData" 
    labelKey="kategori"
    bar1Key="bulan_lalu"
    bar1Label="Bulan Lalu"
    bar1Color="rgba(15, 23, 42, 0.15)" 
    bar2Key="bulan_ini"
    bar2Label="Bulan Ini"
    bar2Color="#3b82f6"
/>