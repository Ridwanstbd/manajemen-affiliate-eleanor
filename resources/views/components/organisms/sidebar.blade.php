<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">L</div>
        <span class="brand-text">LedgerFlow</span>
    </div>

    <nav class="nav-section">
        <div class="nav-label">Main Console</div>
        <div class="nav-list">
            <x-molecules.nav-item icon="dashboard" label="Dashboard" href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" />
            <x-molecules.nav-item icon="cash-flow" label="Cash Flow" />
            <x-molecules.nav-item icon="profit-loss" label="Profit & Loss" />
            <x-molecules.nav-item icon="balance-sheet" label="Balance Sheet" />
        </div>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Transactions</div>
        <div class="nav-list">
            <x-molecules.nav-item icon="invoices" label="Invoices" href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')" />
            <x-molecules.nav-item icon="expenses" label="Expenses" />
            <x-molecules.nav-item icon="payments" label="Payments" />
            <x-molecules.nav-item icon="journal" label="Journal Entries" />
        </div>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Management</div>
        <div class="nav-list">
            <x-molecules.nav-item icon="customers" label="Customers" />
            <x-molecules.nav-item icon="vendors" label="Vendors" />
            <x-molecules.nav-item icon="reports" label="Reports" />
        </div>
    </nav>
</aside>