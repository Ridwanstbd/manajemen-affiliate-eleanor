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
        
        <x-atoms.search-input placeholder="Search transactions, invoices..." />
    </div>

    <div class="header-actions">
        <button class="header-btn">
            <x-atoms.icon name="bell" style="width: 28px; height: 28px;" />
            <span class="badge"></span>
        </button>

        <x-molecules.dropdown>
            <x-slot:trigger>
                <div class="company-dropdown">
                    <span class="company-name">PT Maju Jaya</span>
                    <x-atoms.icon name="chevron-down" class="company-arrow" style="width: 14px; height: 14px;" />
                </div>
            </x-slot:trigger>

            <x-atoms.dropdown-item href="{{ route('dashboard') }}">
                Dashboard
            </x-atoms.dropdown-item>
            
            <x-atoms.dropdown-item href="#">
                Pengaturan
            </x-atoms.dropdown-item>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-atoms.dropdown-item href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    Keluar
                </x-atoms.dropdown-item>
            </form>
        </x-molecules.dropdown>

        <x-atoms.avatar initials="JD" />
    </div>
    @endif
</header>