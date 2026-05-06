<x-molecules.card  title="Papan Peringkat Bulanan">
    
    <x-slot name="headerAction">
        <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500; font-size: 13px; padding: 6px 12px;">
            <x-atoms.icon name="journal" style="width: 16px; height: 16px; margin-right: 6px; display: inline-block; vertical-align: middle;" />
            Filter Bulan
        </x-atoms.button>
    </x-slot>

    @php
        $headers = [
            ['label' => 'PERINGKAT'], ['label' => 'USERNAME TIKTOK'], ['label' => 'TOTAL PESANAN'], 
            ['label' => 'VIDEO/LIVE'], ['label' => 'GMV AFILIASI', 'align' => 'right']
        ];
        $monthlyLeaders = [
            ['#1', '@sarah.beauty', '1,245', '12 / 4', 'Rp 45.2M'],
            ['#2', '@mike.skincare', '980', '8 / 2', 'Rp 38.1M'],
            ['#3', '@emily.glow', '850', '15 / 0', 'Rp 32.9M'],
            ['#4', '@alex.review', '710', '5 / 5', 'Rp 28.4M'],
        ];
    @endphp

    <x-organisms.glass-table :headers="$headers">
        @foreach($monthlyLeaders as $row)
        <tr>
            <td><x-atoms.badge status="{{ $loop->first ? 'paid' : 'pending' }}">{{ $row[0] }}</x-atoms.badge></td>
            <td style="font-weight: 600; color: var(--text-primary);">{{ $row[1] }}</td>
            <td>{{ $row[2] }}</td>
            <td>{{ $row[3] }}</td>
            <td style="text-align: right; font-weight: 700; color: var(--text-primary);">{{ $row[4] }}</td>
        </tr>
        @endforeach
    </x-organisms.glass-table>

    <div style="margin-top: 16px;">
        <div style="border-bottom: 1px solid rgba(0,0,0,0.05); height: 40px;"></div>
        <div style="border-bottom: 1px solid rgba(0,0,0,0.05); height: 40px;"></div>
    </div>
</x-molecules.card>