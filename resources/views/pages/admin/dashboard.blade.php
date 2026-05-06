@extends('layouts.app')

@section('title', 'Dashboard Administrator')

@section('content')
    <div class="stats-container">
        <x-molecules.stat-card 
            color="emerald" 
            icon="revenue" 
            trend="{{ $stats['gmv']['dir'] }}" 
            trendValue="{{ number_format($stats['gmv']['trend'], 1, ',', '.') }}%" 
            value="Rp {{ number_format($stats['gmv']['value'], 0, ',', '.') }}" 
            label="Total GMV Affiliate" 
        />
        
        <x-molecules.stat-card 
            color="blue" 
            icon="profit-loss" 
            trend="{{ $stats['items']['dir'] }}" 
            trendValue="{{ number_format($stats['items']['trend'], 1, ',', '.') }}%" 
            value="{{ number_format($stats['items']['value'], 0, ',', '.') }}" 
            label="Pesanan Terjual" 
        />
        
        <x-molecules.stat-card 
            color="amber" 
            icon="commision" 
            trend="{{ $stats['commission']['dir'] }}" 
            trendValue="{{ number_format($stats['commission']['trend'], 1, ',', '.') }}%" 
            value="Rp {{ number_format($stats['commission']['value'], 0, ',', '.') }}" 
            label="Estimasi Komisi" 
        />
        
        <x-molecules.stat-card 
            color="rose" 
            icon="expenses" 
            trend="{{ $stats['refunds']['dir'] }}" 
            trendValue="{{ number_format($stats['refunds']['trend'], 1, ',', '.') }}%" 
            value="Rp {{ number_format($stats['refunds']['value'], 0, ',', '.') }}" 
            label="Pengembalian Dana" 
        />
    </div>

    <div class="dashboard-grid" style="margin-top: 20px;">
        
        <x-organisms.bar-chart 
            title="Tren Performa (6 Bulan Terakhir)" 
            :data="$chartData" 
            labelKey="bulan"
            
            bar1Key="gmv_percent"
            bar1ValueKey="gmv"
            bar1Label="Tren GMV"
            bar1Color="#3b82f6" 
            
            bar2Key="items_percent"
            bar2ValueKey="items"
            bar2Label="Tren Penjualan"
            bar2Color="#10b981"
        />

        <x-organisms.donut-chart-card 
            title="Status Permintaan Sampel" 
            :data="$sampleStatusData" 
        />
        
    </div>

    <div class="dashboard-grid" style="margin-top: 20px;">
        
        <x-molecules.card title="5 Kreator Teratas">
            <div class="table-responsive" style="overflow-x: auto; margin-top: 10px;">
                <table class="w-100" style="text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 13px;">
                            <th style="padding: 10px 8px; font-weight: 600;">Kreator</th>
                            <th style="padding: 10px 8px; font-weight: 600;">Barang Terjual</th>
                            <th style="padding: 10px 8px; font-weight: 600;">Estimasi Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCreators as $creator)
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 14px;">
                            <td style="padding: 12px 8px; font-weight: 500;">
                                {{ $creator->user->username ?? 'Unknown' }}
                            </td>
                            <td style="padding: 12px 8px;">
                                {{ number_format($creator->total_items, 0, ',', '.') }}
                            </td>
                            <td style="padding: 12px 8px; color: var(--emerald); font-weight: 600;">
                                Rp {{ number_format($creator->total_commission, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 16px; text-align: center; color: var(--text-tertiary); font-size: 13px;">
                                Belum ada data kreator
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-molecules.card>

        <x-molecules.card title="Tugas Tertunda" description="Item yang memerlukan persetujuan administrator">
            <div style="margin-top: 16px;">
                @forelse($pendingTasksList as $task)
                    <a href="{{ $task->route }}" class="task-action-item">
                        <div class="task-action-avatar"></div>
                        <div class="task-action-content">
                            <h4 class="task-action-title">{{ $task->title }}</h4>
                            <p class="task-action-subtitle">{{ $task->name }} &bull; {{ $task->time }}</p>
                        </div>
                        <div class="task-action-chevron">&gt;</div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 24px 0; color: var(--text-tertiary);">
                        <p>Tidak ada tugas tertunda saat ini.</p>
                    </div>
                @endforelse
            </div>
        </x-molecules.card>
    </div>
@endsection