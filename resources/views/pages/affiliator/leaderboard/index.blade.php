@extends('layouts.app')

@section('title', 'Papan Peringkat')

@section('content')
<x-organisms.mobile-page-wrapper title="Papan Peringkat" subtitle="Pantau posisi Anda dan raih peringkat teratas!">

    <x-molecules.glass-tabs>
        <x-molecules.glass-tab-item
            :active="$currentTab === 'monthly'"
            href="{{ route('affiliator.leaderboard.index', ['tab' => 'monthly']) }}">
            Peringkat Bulanan
        </x-molecules.glass-tab-item>
        <x-molecules.glass-tab-item
            :active="$currentTab === 'challenge'"
            href="{{ route('affiliator.leaderboard.index', ['tab' => 'challenge']) }}">
            Tantangan Spesifik
        </x-molecules.glass-tab-item>
    </x-molecules.glass-tabs>

    <div style="margin-top: 24px;">

        @if($currentTab === 'monthly')

            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="flex: 1;">
                    <x-atoms.select onchange="window.location.href=this.value" style="font-weight: 700; font-size: 14px; border-radius: 10px;">
                        @foreach($availableMonths as $month)
                            <option value="{{ route('affiliator.leaderboard.index', ['tab' => 'monthly', 'month' => $month['value']]) }}"
                                {{ $selectedMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </x-atoms.select>
                </div>
                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                    <x-atoms.icon name="trend-up" style="width: 15px; height: 15px; color: var(--text-tertiary);" />
                    <x-atoms.typography variant="body" style="font-size: 12.5px; color: var(--text-tertiary); white-space: nowrap;">
                        Berdasarkan GMV
                    </x-atoms.typography>
                </div>
            </div>

            <x-molecules.card style="border: 1px solid rgba(59,130,246,0.2); border-radius: 16px; padding: 16px; margin-bottom: 6px; background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(255,255,255,0.9));">
                <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 12px;">
                    Posisi Anda Saat Ini
                </x-atoms.typography>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @php $myRank = $currentUser ? $currentUser->rank : null; @endphp
                        <div style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0;
                            {{ $myRank == 1 ? 'background: linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff;' : ($myRank == 2 ? 'background: linear-gradient(135deg,#94a3b8,#64748b); color:#fff;' : ($myRank == 3 ? 'background: linear-gradient(135deg,#d97706,#b45309); color:#fff;' : 'background: rgba(59,130,246,0.1); color: var(--primary-blue);')) }}">
                            {{ $myRank ?? '-' }}
                        </div>
                        <div>
                            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                {{ '@' . auth()->user()->username }}
                                <span style="font-size: 12px; font-weight: 600; color: var(--primary-blue);">(Anda)</span>
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 12.5px; color: var(--text-secondary); display: block;">
                                Barang Terjual: {{ $currentUser ? number_format($currentUser->total_items_sold, 0, ',', '.') : '0' }} Item
                            </x-atoms.typography>
                        </div>
                    </div>
                    <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--primary-blue);">
                        {{ $currentUser ? $currentUser->formatted_gmv : 'Rp 0' }}
                    </x-atoms.typography>
                </div>
            </x-molecules.card>

            <div style="border-top: 1px dashed rgba(0,0,0,0.08); margin: 20px 0 20px;"></div>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                @foreach($topLeaders as $leader)
                    @php
                        $isTop3 = $leader->rank <= 3;
                        $isMe   = $leader->user_id === auth()->user()->id;
                        $rankBg = match($leader->rank) {
                            1 => 'linear-gradient(135deg,#fbbf24,#f59e0b)',
                            2 => 'linear-gradient(135deg,#94a3b8,#64748b)',
                            3 => 'linear-gradient(135deg,#d97706,#b45309)',
                            default => $isMe ? 'rgba(59,130,246,0.1)' : '#f1f5f9',
                        };
                        $rankColor = $isTop3 ? '#fff' : ($isMe ? 'var(--primary-blue)' : 'var(--text-secondary)');
                    @endphp
                    <x-molecules.card style="padding: 14px 16px; border-radius: 14px; border: {{ $isMe ? '2px solid var(--primary-blue)' : '1px solid var(--glass-border)' }}; background: {{ $isMe ? 'rgba(59,130,246,0.04)' : '#ffffff' }}; box-shadow: {{ $isMe ? '0 4px 16px rgba(59,130,246,0.1)' : 'none' }};">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; flex-shrink: 0; background: {{ $rankBg }}; color: {{ $rankColor }};">
                                    {{ $leader->rank }}
                                </div>
                                <div>
                                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14.5px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                        {{ '@' . $leader->user->username }}
                                        @if($isMe)
                                            <span style="font-size: 11px; font-weight: 600; color: var(--primary-blue);">(Anda)</span>
                                        @endif
                                    </x-atoms.typography>
                                    <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); display: block;">
                                        Barang Terjual: {{ number_format($leader->total_items_sold, 0, ',', '.') }} Item
                                    </x-atoms.typography>
                                </div>
                            </div>
                            <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 14.5px; color: {{ $isMe ? 'var(--primary-blue)' : 'var(--text-primary)' }};">
                                {{ $leader->formatted_gmv }}
                            </x-atoms.typography>
                        </div>
                    </x-molecules.card>
                @endforeach
            </div>

        @else
            <div style="margin-bottom: 20px;">
                <x-atoms.select onchange="window.location.href=this.value" style="font-weight: 700; font-size: 14px; border-radius: 10px; width: 100%;">
                    @foreach($availableChallenges as $c)
                        <option value="{{ route('affiliator.leaderboard.index', ['tab' => 'challenge', 'challenge_id' => $c['value']]) }}"
                            {{ $selectedChallengeId == $c['value'] ? 'selected' : '' }}>
                            {{ $c['label'] }}
                        </option>
                    @endforeach
                </x-atoms.select>
            </div>

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
                                Peringkat dinilai berdasarkan kuantitas unggahan video yang disetujui selama periode tantangan.
                            </x-atoms.typography>
                        </div>
                    </div>
                    @if($challenge->rewards->count() > 0)
                        <div style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: rgba(245,158,11,0.08); border-radius: 10px; border: 1px solid rgba(245,158,11,0.2);">
                            <x-atoms.icon name="medal-ribbon" style="width: 16px; height: 16px; color: #d97706; flex-shrink: 0;" />
                            <x-atoms.typography variant="body" style="font-weight: 700; font-size: 13px; color: #b45309;">
                                Total Hadiah: {{ $challenge->rewards->first()->reward_description }}
                            </x-atoms.typography>
                        </div>
                    @endif
                </x-molecules.card>

                @php
                    $target         = ($challenge->rewards && $challenge->rewards->first()) ? $challenge->rewards->first()->target_value : 0;
                    $currentVideos  = $currentUser ? $currentUser->total_videos : 0;
                    $shortfall      = max(0, $target - $currentVideos);
                    $myRank         = $currentUser ? $currentUser->rank : null;
                @endphp
                <x-molecules.card style="border: 1px solid rgba(59,130,246,0.2); border-radius: 16px; padding: 16px; margin-bottom: 6px; background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(255,255,255,0.9));">
                    <x-atoms.typography variant="body" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 12px;">
                        Posisi Anda Saat Ini
                    </x-atoms.typography>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0;
                                {{ $myRank == 1 ? 'background: linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff;' : ($myRank == 2 ? 'background: linear-gradient(135deg,#94a3b8,#64748b); color:#fff;' : ($myRank == 3 ? 'background: linear-gradient(135deg,#d97706,#b45309); color:#fff;' : 'background: rgba(59,130,246,0.1); color: var(--primary-blue);')) }}">
                                {{ $myRank ?? '-' }}
                            </div>
                            <div>
                                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                    {{ '@' . auth()->user()->username }}
                                    <span style="font-size: 12px; font-weight: 600; color: var(--primary-blue);">(Anda)</span>
                                </x-atoms.typography>
                                <x-atoms.typography variant="body" style="font-size: 12.5px; color: var(--text-secondary); display: block;">
                                    Kekurangan target: {{ $shortfall }} Video
                                </x-atoms.typography>
                            </div>
                        </div>
                        <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--primary-blue);">
                            {{ $currentVideos }} Video
                        </x-atoms.typography>
                    </div>
                </x-molecules.card>

                <div style="border-top: 1px dashed rgba(0,0,0,0.08); margin: 20px 0;"></div>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px;">
                    @foreach($topLeaders as $leader)
                        @php
                            $isTop3 = $leader->rank <= 3;
                            $isMe   = $leader->user_id === auth()->user()->id;
                            $rankBg = match($leader->rank) {
                                1 => 'linear-gradient(135deg,#fbbf24,#f59e0b)',
                                2 => 'linear-gradient(135deg,#94a3b8,#64748b)',
                                3 => 'linear-gradient(135deg,#d97706,#b45309)',
                                default => $isMe ? 'rgba(59,130,246,0.1)' : '#f1f5f9',
                            };
                            $rankColor = $isTop3 ? '#fff' : ($isMe ? 'var(--primary-blue)' : 'var(--text-secondary)');
                        @endphp
                        <x-molecules.card style="padding: 14px 16px; border-radius: 14px; border: {{ $isMe ? '2px solid var(--primary-blue)' : '1px solid var(--glass-border)' }}; background: {{ $isMe ? 'rgba(59,130,246,0.04)' : '#ffffff' }}; box-shadow: {{ $isMe ? '0 4px 16px rgba(59,130,246,0.1)' : 'none' }};">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; flex-shrink: 0; background: {{ $rankBg }}; color: {{ $rankColor }};">
                                        {{ $leader->rank }}
                                    </div>
                                    <x-atoms.typography variant="body" style="font-weight: 800; font-size: 14.5px; color: var(--text-primary); display: block;">
                                        {{ '@' . $leader->user->username }}
                                        @if($isMe)
                                            <span style="font-size: 11px; font-weight: 600; color: var(--primary-blue);">(Anda)</span>
                                        @endif
                                    </x-atoms.typography>
                                </div>
                                <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 14.5px; color: {{ $isMe ? 'var(--primary-blue)' : 'var(--text-primary)' }};">
                                    {{ $leader->total_videos }} Video
                                </x-atoms.typography>
                            </div>
                        </x-molecules.card>
                    @endforeach
                </div>

            @else
                <div style="text-align: center; padding: 48px 20px; background: rgba(255,255,255,0.5); border-radius: 20px; border: 1px dashed var(--glass-border);">
                    <x-atoms.icon name="medal" style="width: 40px; height: 40px; color: var(--text-tertiary); margin-bottom: 12px; opacity: 0.4;" />
                    <x-atoms.typography variant="body" style="font-size: 14px; color: var(--text-secondary); display: block;">
                        Belum ada tantangan aktif.
                    </x-atoms.typography>
                </div>
            @endif

        @endif

    </div>
</x-organisms.mobile-page-wrapper>
@endsection