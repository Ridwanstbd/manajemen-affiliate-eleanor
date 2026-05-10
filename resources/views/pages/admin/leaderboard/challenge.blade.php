<x-molecules.card title="Papan Peringkat: {{ $challenge->title ?? 'Tantangan' }}">
    
    <x-slot name="headerAction">
        <div style="display: flex; gap: 16px; align-items: center;">
            <x-molecules.dropdown>
                <x-slot:trigger>
                    <x-atoms.button variant="secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-primary); font-weight: 500;">
                        <x-atoms.icon name="reports" style="width: 16px; height: 16px; margin-right: 6px; display: inline-block; vertical-align: middle;" />
                        {{ $challenge->title ?? 'Pilih Tantangan' }}
                    </x-atoms.button>
                </x-slot:trigger>

                @forelse($availableChallenges as $c)
                    <x-atoms.dropdown-item href="?tab={{ $currentTab }}&selected_challenge={{ $c['value'] }}">
                        {{ $c['label'] }}
                    </x-atoms.dropdown-item>
                @empty
                    <x-atoms.dropdown-item>Belum ada tantangan</x-atoms.dropdown-item>
                @endforelse
            </x-molecules.dropdown>
        </div>
    </x-slot>

    @php
        $headers = [
            ['label' => 'PERINGKAT'], ['label' => 'USERNAME TIKTOK'], ['label' => 'TOTAL PESANAN'], 
            ['label' => 'VIDEO/LIVE'], ['label' => 'GMV AFILIASI', 'align' => 'right']
        ];
    @endphp

    <x-organisms.glass-table :headers="$headers">
        @forelse($leadersData as $row)
        <tr>
            <td><x-atoms.badge status="{{ $loop->first ? 'paid' : 'pending' }}">{{ $row[0] }}</x-atoms.badge></td>
            <td style="font-weight: 600; color: var(--text-primary);">{{ $row[1] }}</td>
            <td>{{ $row[2] }}</td>
            <td>{{ $row[3] }}</td>
            <td style="text-align: right; font-weight: 700; color: var(--text-primary);">{{ $row[4] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">Tidak ada data untuk tantangan ini.</td>
        </tr>
        @endforelse
    </x-organisms.glass-table>

    <div style="margin-top: 16px;">
        <div style="border-bottom: 1px solid rgba(0,0,0,0.05); height: 40px;"></div>
        <div style="border-bottom: 1px solid rgba(0,0,0,0.05); height: 40px;"></div>
    </div>
</x-molecules.card>