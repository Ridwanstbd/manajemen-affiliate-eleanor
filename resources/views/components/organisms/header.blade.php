<header class="header">
    @if(View::hasSection('is_subpage'))
        <a href="{{ url()->previous() }}" class="header-btn back-btn" aria-label="Kembali">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="subpage-title">@yield('title')</h2>
        <div style="width: 40px;"></div>
    @else
    <div class="header-left">
        @if(!auth()->check() || auth()->user()->role !== 'AFFILIATOR')
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <x-atoms.icon name="menu" id="menu-toggle" style="width: 28px; height: 28px; pointer-events: none;" />
        </button>
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

        <x-molecules.dropdown>
            <x-slot:trigger>
                <div class="header-btn">
                    <x-atoms.icon name="profile" style="width: 28px; height: 28px;"/>
                </div>
            </x-slot:trigger>

            <x-atoms.dropdown-item href="{{ route('admin-dashboard.dashboard') }}">
                Dashboard
            </x-atoms.dropdown-item>
                        
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-atoms.dropdown-item href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    Keluar
                </x-atoms.dropdown-item>
            </form>
        </x-molecules.dropdown>

    </div>
    @endif
</header>

<x-organisms.offcanvas id="notificationOffcanvas" title="Notifikasi Admin">
    <div style="padding: 24px; padding-top: 0;">
            
        <x-atoms.typography variant="card-title" as="h4" style="margin-bottom: 16px; font-size: 14px; color: var(--text-secondary);">
                Tugas yang Menunggu Persetujuan
        </x-atoms.typography>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($pendingTasksList ?? [] as $task)<a href="{{ $task->route }}" style="display: block; padding: 16px; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border, #cbd5e1); border-radius: 8px; text-decoration: none; transition: 0.2s;">
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
                    <x-atoms.icon name="bell" style="width: 48px; height: 48px; color: var(--text-tertiary); margin-bottom: 16px; opacity: 0.5;" />
                    <p style="font-size: 14px; color: var(--text-secondary);">Semua sudah selesai! Tidak ada tugas tertunda.</p>
                </div>
            @endforelse
        </div>

    </div>
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