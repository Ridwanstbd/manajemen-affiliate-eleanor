@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
    @php
        $chartData = [
            ['label' => 'Jan', 'income' => 60, 'expense' => 40],
            ['label' => 'Feb', 'income' => 75, 'expense' => 45],
            ['label' => 'Mar', 'income' => 65, 'expense' => 50],
            ['label' => 'Apr', 'income' => 85, 'expense' => 55],
            ['label' => 'Mei', 'income' => 70, 'expense' => 48],
            ['label' => 'Jun', 'income' => 90, 'expense' => 62],
        ];
    @endphp

    <div class="stats-container">
        <x-molecules.stat-card 
            color="emerald" 
            icon="revenue" 
            trend="up" 
            trendValue="12.5%" 
            value="Rp 24.500.000" 
            label="Total Pendapatan" 
        />
        
        <x-molecules.stat-card 
            color="rose" 
            icon="expenses" 
            trend="down" 
            trendValue="4.2%" 
            value="Rp 8.200.000" 
            label="Total Pengeluaran" 
        />
        
        <x-molecules.stat-card 
            color="blue" 
            icon="profit-loss" 
            trend="up" 
            trendValue="18.1%" 
            value="Rp 16.300.000" 
            label="Keuntungan Bersih" 
        />
        
        <x-molecules.stat-card 
            color="amber" 
            icon="balance-sheet" 
            trend="up" 
            trendValue="5.0%" 
            value="Rp 120.000.000" 
            label="Saldo Kas" 
        />
    </div>

    <div class="dashboard-grid" style="margin-top: 20px;">
        
        <x-organisms.chart-card 
            title="Tren Arus Kas" 
            :data="$chartData" 
        />

        <x-molecules.card title="Aktivitas Terbaru" description="Transaksi masuk dan keluar hari ini">
            <div class="timeline">
                <x-molecules.timeline-item 
                    type="received" 
                    icon="arrow-down-left" 
                    title="Pembayaran Klien" 
                    amount="+ Rp 5.000.000" 
                    amountType="positive" 
                    status="paid" 
                    time="Hari ini, 09:30" 
                />
                <x-molecules.timeline-item 
                    type="expense" 
                    icon="expenses" 
                    title="Pembelian Aset" 
                    amount="- Rp 1.200.000" 
                    amountType="negative" 
                    status="paid" 
                    time="Kemarin, 14:15" 
                />
                <x-molecules.timeline-item 
                    type="refund" 
                    icon="refresh" 
                    title="Pengembalian Dana" 
                    amount="+ Rp 300.000" 
                    amountType="positive" 
                    status="pending" 
                    time="12 Mei, 10:00" 
                />
            </div>
        </x-molecules.card>
        
    </div>

    <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; margin-top: 20px;">
        
        <x-molecules.card title="5 Kreator Teratas">
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                <x-atoms.button variant="primary" type="button" size="md">
                    <x-atoms.icon name="plus" style="width: 18px; height: 18px;" />
                    Buat Transaksi
                </x-atoms.button>
                
                <x-atoms.button variant="secondary" type="button" size="md">
                    <x-atoms.icon name="download" style="width: 18px; height: 18px;" />
                    Unduh Laporan
                </x-atoms.button>
            </div>
        </x-molecules.card>

        <x-molecules.card title="Tugas Tertunda" description="Dokumen yang memerlukan persetujuan">
            <div class="timeline">
                <x-molecules.timeline-item 
                    type="refund" 
                    icon="invoices" 
                    title="Review Invoice #INV-2026" 
                    amount="Rp 4.500.000" 
                    amountType="" 
                    status="pending" 
                    time="Tenggat: Besok" 
                />
                <x-molecules.timeline-item 
                    type="expense" 
                    icon="commision" 
                    title="Pencairan Komisi Afiliator" 
                    amount="Rp 1.500.000" 
                    amountType="" 
                    status="overdue" 
                    time="Tenggat: Terlewat" 
                />
            </div>
        </x-molecules.card>

    </div>
@endsection