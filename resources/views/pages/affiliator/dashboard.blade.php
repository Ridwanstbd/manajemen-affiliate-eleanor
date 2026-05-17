@extends('layouts.app')

@section('title', 'Dashboard Affiliator')

@section('content')
    @if(isset($activeChallenges) && $activeChallenges->count() > 0)
        <x-organisms.carousel :count="$activeChallenges->count()">
            @foreach($activeChallenges as $challenge)
                <x-molecules.challenge-banner-slide 
                    :link="route('affiliator.challenge.show', $challenge->id)"
                    tag="Tantangan Aktif 🔥"
                    :title="$challenge->title"
                    :description="Str::limit($challenge->rules, 110)" 
                    :banner="$challenge->banner_image_path ? asset('storage/' . $challenge->banner_image_path) : null"
                    icon="trophy"
                />
            @endforeach
        </x-organisms.carousel>
    @endif
    <div class="stats-container">
        <x-molecules.stat-card 
            color="emerald" 
            icon="revenue" 
            trend="{{ $stats['gmv']['dir'] }}" 
            trendValue="{{ number_format($stats['gmv']['trend'], 1, ',', '.') }}%" 
            value="Rp {{ number_format($stats['gmv']['value'], 0, ',', '.') }}" 
            label="Total Penjualan Anda" 
        />
        
        <x-molecules.stat-card 
            color="blue" 
            icon="profit-loss" 
            trend="{{ $stats['items']['dir'] }}" 
            trendValue="{{ number_format($stats['items']['trend'], 1, ',', '.') }}%" 
            value="{{ number_format($stats['items']['value'], 0, ',', '.') }}" 
            label="Barang Terjual" 
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
            label="Total Refund" 
        />
    </div>

    <div class="dashboard-grid" style="margin-top: 20px;">
        <x-organisms.bar-chart 
            title="Tren Performa Pribadi" 
            :data="$chartData" 
            labelKey="bulan"
            
            bar1Key="gmv_percent"
            bar1ValueKey="gmv"
            bar1Label="GMV"
            bar1Color="#3b82f6" 
            
            bar2Key="items_percent"
            bar2ValueKey="items"
            bar2Label="Produk"
            bar2Color="#10b981"
        />
        <x-molecules.card title="Status Sampel Produk" description="Pantau pengajuan sampel gratis Anda">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                <div style="padding: 16px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                    <div style="font-size: 12px; color: #b45309; font-weight: 600;">Menunggu</div>
                    <div style="font-size: 24px; font-weight: 800; color: #b45309;">{{ $sampleSummary['pending'] }}</div>
                </div>
                <div style="padding: 16px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <div style="font-size: 12px; color: var(--primary-blue); font-weight: 600;">Disetujui</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--primary-blue);">{{ $sampleSummary['approved'] }}</div>
                </div>
                <div style="padding: 16px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                    <div style="font-size: 12px; color: var(--emerald); font-weight: 600;">Dikirim</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--emerald);">{{ $sampleSummary['shipped'] }}</div>
                </div>
                <div style="padding: 16px; border-radius: 12px; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2);">
                    <div style="font-size: 12px; color: var(--rose); font-weight: 600;">Ditolak</div>
                    <div style="font-size: 24px; font-weight: 800; color: var(--rose);">{{ $sampleSummary['rejected'] }}</div>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <x-atoms.button variant="outline" style="width: 100%;" href="{{route('affiliator.sample-request.index')}}">
                    Lihat Semua Pengajuan
                </x-atoms.button>
            </div>
        </x-molecules.card>
    </div>
    <div style="margin-top: 20px;">
        <x-molecules.card title="Tugas Video TikTok" description="Daftar konten yang harus diunggah berdasarkan sampel yang diterima">
            <div style="margin-top: 16px;">
                @forelse($pendingTasks as $task)
                    <div class="task-action-item">
                        <div class="task-action-content">
                            <h4 class="task-action-title">
                                Unggah Video: 
                                @foreach($task->products as $product)
                                    {{ $product->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </h4>
                            <p class="task-action-subtitle">
                                Deadline: <span style="color: {{ $task->due_date->isPast() ? 'var(--rose)' : 'var(--text-secondary)' }}; font-weight: 600;">
                                    {{ $task->due_date->translatedFormat('d F Y') }}
                                </span>
                                &bull; Status: {{ $task->task_status }}
                            </p>
                        </div>
                        <x-atoms.button variant="primary" size="sm" onclick="window.location.href='#'">
                            Kirim Link
                        </x-atoms.button>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px 0; color: var(--text-tertiary);">
                        <x-atoms.icon name="check-circle" style="width: 40px; height: 40px; margin-bottom: 12px; opacity: 0.5;" />
                        <p>Tidak ada tugas video yang tertunda. Luar biasa!</p>
                    </div>
                @endforelse
            </div>
        </x-molecules.card>
    </div>
@endsection