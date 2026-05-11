<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('img\logo.png') }}" class="brand-icon" alt="Logo">
        <span class="brand-text">Affiliate</span>
    </div>

    <div class="sidebar-body">
        <nav class="nav-section">
            <div class="nav-label">Intelegensi Bisnis</div>
            <div class="nav-list">
                <x-molecules.nav-item icon="dashboard" label="Dasbor Administrator" href="{{ route('admin-dashboard.dashboard') }}" :active="request()->routeIs('admin-dashboard.dashboard')" />
                <x-molecules.nav-item icon="download" label="Import Data Analitik" href="{{ route('admin-dashboard.import') }}" :active="request()->routeIs('admin-dashboard.import')" />
                <x-molecules.nav-item icon="reports" label="Pusat Analisa" href="{{ route('admin-dashboard.analytics') }}" :active="request()->routeIs('admin-dashboard.analytics')" />
            </div>
        </nav>
    
        <nav class="nav-section">
            <div class="nav-label">Manajemen Kemitraan</div>
            <div class="nav-list">
                <x-molecules.nav-item icon="customers" label="Kelola Affiliator" href="{{ route('admin-dashboard.users.index') }}" :active="request()->routeIs('admin-dashboard.users.index')"/>
                <x-molecules.nav-item icon="eye" label="Monitoring Tugas" />
                <x-molecules.nav-item icon="check" label="Kelola Persetujuan" href="{{ route('admin-dashboard.agreements.index') }}" :active="request()->routeIs('admin-dashboard.agreements.index')"/>
            </div>
        </nav>
    
        <nav class="nav-section">
            <div class="nav-label">Operasional & Inventaris</div>
            <div class="nav-list">
                <x-molecules.nav-item icon="vendors" label="Kelola Sampel Produk" href="{{ route('admin-dashboard.product-index') }}" :active="request()->routeIs('admin-dashboard.product-index')" />
                <x-molecules.nav-item icon="invoices" label="Persetujuan & Pengiriman" />
            </div>
        </nav>
        <nav class="nav-section">
            <div class="nav-label">Program Strategis</div>
            <div class="nav-list">
                <x-molecules.nav-item icon="trend-up" label="Kelola Tantangan" />
            </div>
        </nav>
    </div>
</aside>