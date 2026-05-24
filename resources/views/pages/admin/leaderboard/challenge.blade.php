<x-molecules.card title="Papan Peringkat: Tantangan Spesifik">

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

    @if($challenge)

        <x-molecules.card style="border: 1px solid var(--glass-border); border-radius: 16px; padding: 16px; margin-bottom: 16px; background: rgba(255,255,255,0.8);">
            <div style="display: flex; gap: 14px; margin-bottom: 12px;">
                <div style="width: 64px; height: 64px; border-radius: 12px; overflow: hidden; background: #f1f5f9; border: 1px solid var(--glass-border); flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                    @if($challenge->banner_image_path)
                        <img src="{{ asset('storage/'.$challenge->banner_image_path) }}" alt="Banner" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <x-atoms.icon name="gift" style="width: 28px; height: 28px; color: var(--text-tertiary); opacity: 0.5;" />
                    @endif
                </div>
                <div style="flex: 1; min-width: 0;">
                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 4px;">
                        {{ $challenge->title }}
                    </x-atoms.typography>
                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); display: block; line-height: 1.5;">
                        {{ $challenge->start_date?->translatedFormat('d M Y') }} &mdash; {{ $challenge->end_date?->translatedFormat('d M Y') }}
                    </x-atoms.typography>
                </div>
            </div>
            @if($challenge->rewards->count() > 0)
                <div style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: rgba(245,158,11,0.08); border-radius: 10px; border: 1px solid rgba(245,158,11,0.2);">
                    <x-atoms.icon name="medal-ribbon" style="width: 16px; height: 16px; color: #d97706; flex-shrink: 0;" />
                    <x-atoms.typography variant="body" style="font-size: 12px; color: #92400e; font-weight: 600;">
                        {{ $challenge->rewards->count() }} Hadiah Tersedia
                    </x-atoms.typography>
                </div>
            @endif
        </x-molecules.card>

        @php $winners = $challenge->winners()->with('user')->get(); @endphp

        @if($winners->isNotEmpty())

            <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 12px;">
                Pemenang Tantangan
            </x-atoms.typography>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                @foreach($winners as $index => $winner)
                    @php
                        $podium     = $index + 1;
                        $trophyBg   = match($podium) {
                            1 => 'linear-gradient(135deg,#fbbf24,#f59e0b)',
                            2 => 'linear-gradient(135deg,#94a3b8,#64748b)',
                            3 => 'linear-gradient(135deg,#d97706,#b45309)',
                            default => '#f1f5f9',
                        };
                        $trophyEmoji = match($podium) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => '🏅' };
                    @endphp
                    <x-molecules.card style="padding: 14px 16px; border-radius: 14px; border: 1px solid var(--glass-border); background: #ffffff;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; background: {{ $trophyBg }};">
                                {{ $trophyEmoji }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14.5px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                    {{ '@' . $winner->user->username }}
                                </x-atoms.typography>
                                @if($winner->category)
                                    <x-atoms.typography variant="body" style="font-size: 12px; color: #d97706; font-weight: 600; display: block;">
                                        {{ $winner->category }}
                                    </x-atoms.typography>
                                @endif
                            </div>
                            @if($winner->reward_given)
                                <div style="display: flex; align-items: center; gap: 4px; background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25); border-radius: 8px; padding: 4px 10px; flex-shrink: 0;">
                                    <x-atoms.icon name="check-circle" style="width: 13px; height: 13px; color: #16a34a;" />
                                    <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: #16a34a;">
                                        Hadiah Diberikan
                                    </x-atoms.typography>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 4px; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; padding: 4px 10px; flex-shrink: 0;">
                                    <x-atoms.icon name="clock" style="width: 13px; height: 13px; color: #d97706;" />
                                    <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: #d97706;">
                                        Menunggu
                                    </x-atoms.typography>
                                </div>
                            @endif
                        </div>
                    </x-molecules.card>
                @endforeach
            </div>

        @else

            @if($challenge->rewards->isNotEmpty())
                <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 12px;">
                    Hadiah Tantangan
                </x-atoms.typography>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                    @foreach($challenge->rewards as $reward)
                        <x-molecules.card style="padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(245,158,11,0.2); background: rgba(245,158,11,0.04);">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; background: rgba(245,158,11,0.12);">
                                    <x-atoms.icon name="medal-ribbon"/>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                        {{ $reward->target_metric }}
                                    </x-atoms.typography>
                                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); display: block;">
                                        {{ $reward->reward_description }}
                                    </x-atoms.typography>
                                </div>
                            </div>
                        </x-molecules.card>
                    @endforeach
                </div>
            @endif

        @endif
    @else
        <div style="text-align: center; padding: 48px 20px; background: rgba(255,255,255,0.5); border-radius: 20px; border: 1px dashed var(--glass-border);">
            <x-atoms.icon name="medal" style="width: 40px; height: 40px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.4;" />
            <x-atoms.typography variant="body" style="font-size: 14px; color: var(--text-secondary); display: block;">
                Belum ada tantangan aktif.
            </x-atoms.typography>
        </div>
    @endif

</x-molecules.card>