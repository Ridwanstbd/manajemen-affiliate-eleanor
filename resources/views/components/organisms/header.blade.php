<header class="header">
    @if(View::hasSection('is_subpage'))
        <a href="{{ url()->previous() }}" class="header-btn" aria-label="Kembali">
            <x-atoms.icon name="chevron-left" style="width: 28px; height: 28px;"/>
        </a>
        <h2 class="subpage-title">@yield('title')</h2>
        <div class="" style="width: 240px"></div>
        @if (request()->routeIs('affiliator.catalog.*')) 
        <a href="{{ route('affiliator.cart.index') }}" class="header-btn" aria-label="Keranjang">
            <x-atoms.icon name="cart" style="width: 28px; height: 28px;"/>
            @if($notificationCount > 0)
                <span class="badge">
                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                </span>
            @endif
        </a>
        @endif
    @else
    <div class="header-left">
        @if(!auth()->check() || auth()->user()->role !== 'AFFILIATOR')
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <x-atoms.icon name="menu" id="menu-toggle" style="width: 28px; height: 28px; pointer-events: none;" />
        </button>
        @elseif(auth()->check() && auth()->user()->role === 'AFFILIATOR')
        <div style="display: flex; gap: calc(5%); align-items: center;">
            <img src="{{ asset('img\logo.png') }}" class="brand-icon" alt="Logo">
            <span class="brand-text">Affiliate</span>
        </div>
        @endif
    </div>

    <div class="header-actions">
        <button class="header-btn" onclick="openOffcanvas('notificationOffcanvas')">
            <x-atoms.icon name="bell" style="width: 28px; height: 28px;" />
            
            @if($notificationCount > 0)
                <span class="badge">
                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                </span>
            @endif
        </button>
        @if (request()->routeIs('affiliator.catalog.*')) 
        <a href="{{ route('affiliator.cart.index') }}" class="header-btn" aria-label="Keranjang">
            <x-atoms.icon name="cart" style="width: 28px; height: 28px;"/>
            @if($notificationCount > 0)
                <span class="badge">
                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                </span>
            @endif
        </a>
        @endif
    </div>
    @endif
</header>

<x-organisms.offcanvas id="notificationOffcanvas" title="Notifikasi">
        @if(auth()->check() && auth()->user()->role === 'ADMINISTRATOR')
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 14px; color: var(--text-secondary);">
                Tugas yang Menunggu Persetujuan
            </x-atoms.typography>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($pendingTasksList ?? [] as $task)
                    <a href="{{ $task->route }}" style="display: block; padding: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border, #cbd5e1); border-radius: 8px; text-decoration: none; transition: 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                            <span style="font-size: 14px; font-weight: 600; color: var(--text-primary);">{{ $task->title }}</span>
                            <span style="font-size: 11px; color: var(--primary-blue); background: rgba(59, 130, 246, 0.1); padding: 2px 6px; border-radius: 4px;">Baru</span>
                        </div>
                        <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                            Dari: <strong>{{ $task->name }}</strong>
                        </div>
                        <div style="font-size: 11px; color: var(--text-tertiary);">
                            <x-atoms.icon name="clock" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;" />
                            {{ $task->time }}
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 40px 0;">
                        <x-atoms.icon name="bell" style="width: 28px; height: 28px; color: var(--text-tertiary); margin-bottom: 16px; opacity: 0.5;" />
                        <p style="font-size: 14px; color: var(--text-secondary);">Semua sudah selesai! Tidak ada tugas tertunda.</p>
                    </div>
                @endforelse
            </div>
        @elseif(auth()->check() && auth()->user()->role === 'AFFILIATOR')
            <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 14px; color: var(--text-secondary);">
                Pembaruan Aktivitas Anda
            </x-atoms.typography>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @forelse($affiliatorNotifications ?? [] as $notif)
                    <a href="{{ $notif->route }}" style="display: block; padding: 14px 16px; background: rgba(255,255,255,0.6); border: 1px solid var(--glass-border, #cbd5e1); border-left: 4px solid {{ $notif->color }}; border-radius: 8px; text-decoration: none; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                            {{ $notif->title }}
                        </div>
                        
                        <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; line-height: 1.4;">
                            {{ $notif->desc }}
                        </div>
                        
                        <div style="font-size: 11px; color: var(--text-tertiary);">
                            <x-atoms.icon name="clock" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;" />
                            {{ $notif->time }}
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 40px 0;">
                        <x-atoms.icon name="bell" style="width: 28px; height: 28px; color: var(--text-tertiary); margin-bottom: 16px; opacity: 0.5;" />
                        <p style="font-size: 14px; color: var(--text-secondary);">Belum ada notifikasi aktivitas terbaru.</p>
                    </div>
                @endforelse
            </div>
        @endif
</x-organisms.offcanvas>
<script>
    if (typeof window.openOffcanvas !== 'function') {
        window.openOffcanvas = function(offcanvasId) {
            const offcanvas = document.getElementById(offcanvasId);
            const backdrop = document.getElementById(offcanvasId + '-backdrop');
            if (offcanvas) {
                offcanvas.classList.add('show');
                document.body.style.overflow = 'hidden';
                if(backdrop) backdrop.classList.add('show');
            } else {
                console.error("Offcanvas dengan ID " + offcanvasId + " tidak ditemukan.");
            }
        };
    }

    if (typeof window.toggleOffcanvas !== 'function') {
        window.toggleOffcanvas = function(offcanvasId) {
            const offcanvas = document.getElementById(offcanvasId);
            const backdrop = document.getElementById(offcanvasId + '-backdrop');
            if (offcanvas) offcanvas.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        };
    }
</script>