@extends('layouts.app')

@section('content')
    @php
        $chartData = [
            ['label' => 'Jan', 'income' => 60, 'expense' => 40],
            ['label' => 'Feb', 'income' => 75, 'expense' => 45],
            ['label' => 'Mar', 'income' => 65, 'expense' => 50],
            ['label' => 'Apr', 'income' => 85, 'expense' => 55],
            ['label' => 'May', 'income' => 70, 'expense' => 48],
            ['label' => 'Jun', 'income' => 90, 'expense' => 62],
        ];

        // Helper untuk memformat tampilan trend di view
        $formatTrend = function($value) {
            $formatted = number_format(abs($value), 1) . '%';
            return $value >= 0 ? '+' . $formatted : '-' . $formatted;
        };
    @endphp

    <section class="hero-section">
        <div class="hero-glow"></div>
        <div class="hero-content">
            <x-atoms.typography variant="hero-title">
                Financial Overview – March 2026
            </x-atoms.typography>
            <x-atoms.typography variant="hero-subtitle">
                Real-time performance of your business accounts
            </x-atoms.typography>
            <div class="hero-actions">
                <x-atoms.button variant="primary" onclick="openModal('createTransactionModal')">
                    <x-atoms.icon name="plus" style="width: 15px; height: 15px;" /> Create Transaction
                </x-atoms.button>
                <x-atoms.button variant="secondary">
                    <x-atoms.icon name="download" style="width: 15px; height: 15px;" /> Export Report
                </x-atoms.button>
            </div>
        </div>
    </section>

    <div class="stats-container">
        
        <x-molecules.stat-card 
            color="emerald" 
            icon="revenue" 
            trend="{{ $revenueTrend >= 0 ? 'up' : 'down' }}" 
            trendValue="{{ $formatTrend($revenueTrend) }}" 
            value="Rp {{ number_format($totalRevenue, 0, ',', '.') }}" 
            label="Total Revenue" 
        />
        
        <x-molecules.stat-card 
            color="rose" 
            icon="expense-stat" 
            trend="{{ $expenseTrend <= 0 ? 'up' : 'down' }}" 
            trendValue="{{ $formatTrend($expenseTrend) }}" 
            value="Rp {{ number_format($totalExpenses, 0, ',', '.') }}" 
            label="Total Expenses" 
        />
        
        <x-molecules.stat-card 
            color="blue" 
            icon="profit-stat" 
            trend="{{ $profitTrend >= 0 ? 'up' : 'down' }}" 
            trendValue="{{ $formatTrend($profitTrend) }}" 
            value="Rp {{ number_format($netProfit, 0, ',', '.') }}" 
            label="Net Profit" 
        />
        
        <x-molecules.stat-card 
            color="amber" 
            icon="wallet" 
            trend="{{ $cashTrend >= 0 ? 'up' : 'down' }}" 
            trendValue="{{ $formatTrend($cashTrend) }}" 
            value="Rp {{ number_format($cashOnHand, 0, ',', '.') }}" 
            label="Cash on Hand" 
        />
        
    </div>

    <div class="dashboard-grid">
        <x-organisms.chart-card title="Cash Flow Trend" :data="$chartData" />
    </div>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf        
        <x-organisms.modal 
            id="createTransactionModal" 
            title="Create New Transaction" 
            description="Fill out the details below to record a new transaction."
        >
            
            <div class="form-group">
                <x-atoms.label for="tanggal" value="Date" />
                <x-atoms.input id="tanggal" name="date" type="date" required />
            </div>

            <div class="form-group">
                <x-atoms.label for="pocket_id" value="Select Pocket" />
                <x-atoms.select id="pocket_id" name="pocket_id" required>
                    <option value="" disabled selected>Select Source/Destination of Funds</option>
                    @foreach($pockets as $pocket)
                        <option value="{{ $pocket->id }}">{{ $pocket->name }}</option>
                    @endforeach
                </x-atoms.select>
            </div>

            <div class="form-group">
                <x-atoms.label for="typeSelect" value="Transaction Type" />
                <x-atoms.select id="typeSelect" required onchange="updateCategory()">
                    <option value="" disabled selected>Select Group</option>
                    <option value="revenue" style="color: #10b981;">🟢 Income (Money In)</option>
                    <option value="expense" style="color: #ef4444;">🔴 Expenses (Money Out)</option>
                    <option value="liability">🏦 Obligations (Debts)</option>
                    <option value="equity">💎 Equity (Capital)</option>
                </x-atoms.select>
            </div>

            <div class="form-group">
                <x-atoms.label for="categorySelect" value="Account Category" />
                <x-atoms.select name="account_id" id="categorySelect" required>
                    <option value="" disabled selected>Select Type First</option>
                </x-atoms.select>
            </div>

            <div class="form-group">
                <x-atoms.label for="amount" value="Amount (Rp)" />
                <x-atoms.input type="number" id="amount" name="amount" min="1" required placeholder="0" class="input-amount" />
            </div>                
            
            <x-slot name="footer">
                <x-atoms.button variant="secondary" type="button" onclick="closeModal('createTransactionModal')">
                    Cancel
                </x-atoms.button>
                <x-atoms.button variant="primary" type="submit">
                    <x-atoms.icon name="check" style="width: 15px; height: 15px; margin-right: 6px;" />
                    Save
                </x-atoms.button>
            </x-slot>

        </x-organisms.modal>
    </form>
@endsection

@section('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    }

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    const categories = @json($categories ?? []);

    function updateCategory() {
        const selectedType = document.getElementById('typeSelect').value;
        const categorySelect = document.getElementById('categorySelect');
        
        categorySelect.innerHTML = '<option value="" disabled selected>Select Category</option>';
        
        const filteredAccounts = categories.filter(account => account.type === selectedType);
        
        filteredAccounts.forEach(account => {
            let opt = document.createElement('option');
            opt.value = account.id;
            opt.innerHTML = account.name;
            categorySelect.appendChild(opt);
        });
        
        categorySelect.focus();
    }
</script>
@endsection