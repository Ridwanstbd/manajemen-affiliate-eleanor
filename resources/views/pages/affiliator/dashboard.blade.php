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
                    icon="medal-ribbon"
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
    <div style="margin-top: 20px; margin-bottom: 24px;">
        <x-molecules.card title="Tugas Video TikTok" description="Daftar konten yang harus diunggah berdasarkan sampel yang diterima">
            @php
                $grouped = $pendingTasks->groupBy(function($task) {
                    $product = $task->products->first();
                    return $product ? $product->id : 'no-product';
                });
            @endphp

            <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 14px;">
                @forelse($grouped as $productId => $tasks)
                    @php
                        $product     = $tasks->first()->products->first();
                        $fallback    = 'https://placehold.co/400x400?text=No+Image';
                        $imageUrl    = (!empty($product) && !empty($product->image_path))
                                        ? (filter_var($product->image_path, FILTER_VALIDATE_URL)
                                            ? $product->image_path
                                            : asset('storage/' . $product->image_path))
                                        : $fallback;
                        $hasOverdue  = $tasks->contains(fn($t) =>
                                            $t->task_status === 'OVERDUE' ||
                                            (isset($t->due_date) && \Carbon\Carbon::parse($t->due_date)->isPast()));
                    @endphp

                    <div style="border: 1px solid {{ $hasOverdue ? 'rgba(244,63,94,0.25)' : 'var(--glass-border)' }}; border-radius: 14px; overflow: hidden; background: #ffffff;">

                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: {{ $hasOverdue ? 'rgba(254,242,242,0.8)' : 'rgba(248,250,252,0.9)' }}; border-bottom: 1px solid {{ $hasOverdue ? 'rgba(244,63,94,0.12)' : 'rgba(0,0,0,0.05)' }};">
                            <div style="width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #f1f5f9; border: 1px solid var(--glass-border); flex-shrink: 0;">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name ?? 'Produk' }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='{{ $fallback }}'">
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <span style="font-size: 10px; text-transform: uppercase; font-weight: 700; color: var(--text-tertiary); letter-spacing: 0.4px; display: block;">
                                    {{ $product->category ?? 'Produk' }}
                                </span>
                                <span style="font-size: 13.5px; font-weight: 800; color: var(--text-primary); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; line-height: 1.3;">
                                    {{ $product->name ?? 'Produk Sampel' }}
                                </span>
                            </div>
                            <span style="background: {{ $hasOverdue ? 'rgba(244,63,94,0.1)' : 'rgba(59,130,246,0.08)' }}; color: {{ $hasOverdue ? '#e11d48' : 'var(--primary-blue)' }}; font-size: 10.5px; font-weight: 700; border-radius: 20px; padding: 3px 9px; white-space: nowrap; flex-shrink: 0;">
                                {{ $tasks->count() }} tugas
                            </span>
                        </div>

                        @foreach($tasks as $i => $task)
                            @php
                                $isOverdue = $task->task_status === 'OVERDUE' ||
                                             (isset($task->due_date) && \Carbon\Carbon::parse($task->due_date)->isPast());
                                $isLast    = $i === $tasks->count() - 1;
                            @endphp
                            <a href="{{ route('affiliator.task.show', $task->id) }}"
                               style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; text-decoration: none; {{ !$isLast ? 'border-bottom: 1px solid rgba(0,0,0,0.04);' : '' }} transition: background 0.15s;"
                               onmouseover="this.style.background='rgba(241,245,249,0.7)'"
                               onmouseout="this.style.background='transparent'">

                                <div style="width: 26px; height: 26px; border-radius: 7px; background: {{ $isOverdue ? 'rgba(244,63,94,0.1)' : 'rgba(59,130,246,0.08)' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span style="font-size: 10px; font-weight: 800; color: {{ $isOverdue ? '#e11d48' : 'var(--primary-blue)' }};">#{{ $task->id }}</span>
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <x-atoms.icon name="clock" style="width: 11px; height: 11px; color: {{ $isOverdue ? '#e11d48' : 'var(--text-tertiary)' }}; flex-shrink: 0;" />
                                        <span style="font-size: 11.5px; color: {{ $isOverdue ? '#e11d48' : 'var(--text-secondary)' }}; font-weight: {{ $isOverdue ? '700' : '500' }};">
                                            Tenggat: {{ isset($task->due_date) ? \Carbon\Carbon::parse($task->due_date)->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                    <x-atoms.badge :status="$isOverdue ? 'overdue' : 'pending'">
                                        {{ $isOverdue ? 'Melewati Batas' : 'Diproses' }}
                                    </x-atoms.badge>
                                    <x-atoms.icon name="chevron-right" style="width: 14px; height: 14px; color: var(--text-tertiary);" />
                                </div>

                            </a>
                        @endforeach

                    </div>
                @empty
                    <div style="text-align: center; padding: 32px 0; color: var(--text-tertiary);">
                        <x-atoms.icon name="check-circle" style="width: 40px; height: 40px; margin-bottom: 12px; opacity: 0.5;" />
                        <p style="font-size: 13.5px; margin: 0;">Tidak ada tugas video yang tertunda. Luar biasa!</p>
                    </div>
                @endforelse
            </div>

            @if($grouped->isNotEmpty())
                <div style="margin-top: 16px;">
                    <x-atoms.button variant="outline" style="width: 100%;" href="{{ route('affiliator.task.index') }}">
                        Lihat Semua Tugas
                    </x-atoms.button>
                </div>
            @endif
        </x-molecules.card>
    </div>
@endsection