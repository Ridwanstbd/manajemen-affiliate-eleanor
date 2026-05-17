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
                <div style="width: 150px;">
                    <x-atoms.select onchange="window.location.href=this.value" style="font-weight: 700; font-size: 14px; border-radius: 8px;">
                        @foreach($availableMonths as $month)
                            <option value="{{ route('affiliator.leaderboard.index', ['tab' => 'monthly', 'month' => $month['value']]) }}" 
                                {{ $selectedMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </x-atoms.select>
                </div>
                <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary);">
                    Peringkat berdasarkan GMV
                </x-atoms.typography>
            </div>

            <x-molecules.card >
                <x-atoms.typography variant="body" style="font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; display: block;">
                    Posisi Anda Saat Ini
                </x-atoms.typography>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #333; background: #fff;">
                            {{ $currentUser ? $currentUser->rank : '-' }}
                        </div>
                        <div>
                            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                {{ '@' . auth()->user()->username }} (Anda)
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                                Barang Terjual: {{ $currentUser ? number_format($currentUser->total_items_sold, 0, ',', '.') : '0' }} Item
                            </x-atoms.typography>
                        </div>
                    </div>
                    <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                        {{ $currentUser ? $currentUser->formatted_gmv : 'Rp 0' }}
                    </x-atoms.typography>
                </div>
            </x-molecules.card>

            <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin-bottom: 24px;">

            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
                @foreach($topLeaders as $leader)
                    @php
                        $isTop3 = $leader->rank <= 3;
                        $isMe = $leader->user_id === auth()->user()->id;
                    @endphp
                    <x-molecules.card style="padding: 16px; border: {{ $isMe ? '2px solid #333' : '1px solid #e2e8f0' }}; border-radius: 8px; box-shadow: none; background: {{ $isMe ? '#f8fafc' : '#ffffff' }}; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; border: {{ $isTop3 ? 'none' : '1px solid #cbd5e1' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; color: {{ $isTop3 ? '#fff' : '#333' }}; background: {{ $isTop3 ? '#333' : 'transparent' }};">
                                {{ $leader->rank }}
                            </div>
                            <div>
                                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                    {{ '@' . $leader->user->username }} {{ $isMe ? '(Anda)' : '' }}
                                </x-atoms.typography>
                                <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                                    Barang Terjual: {{ number_format($leader->total_items_sold, 0, ',', '.') }} Item
                                </x-atoms.typography>
                            </div>
                        </div>
                        <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                            {{ $leader->formatted_gmv }}
                        </x-atoms.typography>
                    </x-molecules.card>
                @endforeach
            </div>
        @else
            <div style="margin-bottom: 16px;">
                <x-atoms.select onchange="window.location.href=this.value" style="font-weight: 700; font-size: 14px; border-radius: 8px; width: 100%;">
                    @foreach($availableChallenges as $c)
                        <option value="{{ route('affiliator.leaderboard.index', ['tab' => 'challenge', 'challenge_id' => $c['value']]) }}" 
                            {{ $selectedChallengeId == $c['value'] ? 'selected' : '' }}>
                            {{ $c['label'] }}
                        </option>
                    @endforeach
                </x-atoms.select>
            </div>

            @if($challenge)
                <x-molecules.card style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px; box-shadow: none;">
                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 70px; height: 70px; border-radius: 8px; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            @if($challenge->banner_image_path)
                                <img src="{{ asset('storage/'.$challenge->banner_image_path) }}" alt="Banner" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <x-atoms.icon name="image" style="width: 24px; height: 24px; color: #94a3b8;" />
                            @endif
                        </div>
                        <div>
                            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 4px;">
                                {{ $challenge->title }}
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 12px; color: var(--text-secondary); display: block; line-height: 1.4;">
                                Peringkat ini dinilai berdasarkan kuantitas unggahan video yang disetujui selama periode tantangan.
                            </x-atoms.typography>
                        </div>
                    </div>
                    @if($challenge->rewards->count() > 0)
                        <x-atoms.typography variant="body" style="font-weight: 700; font-size: 13px; color: var(--text-primary); display: block;">
                            Total Hadiah: {{ $challenge->rewards->first()->reward_description }}
                        </x-atoms.typography>
                    @endif
                </x-molecules.card>

                <x-molecules.card >
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #333; background: #fff;">
                            {{ $currentUser ? $currentUser->rank : '-' }}
                        </div>
                        <div>
                            <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block; margin-bottom: 2px;">
                                {{ '@' . auth()->user()->username }} (Anda)
                            </x-atoms.typography>
                            <x-atoms.typography variant="body" style="font-size: 13px; color: var(--text-secondary); display: block;">
                                @php
                                    $target = ($challenge->rewards && $challenge->rewards->first()) ? $challenge->rewards->first()->target_value : 0; 
                                    $currentVideos = $currentUser ? $currentUser->total_videos : 0;
                                    $shortfall = max(0, $target - $currentVideos);
                                @endphp
                                Kekurangan target: {{ $shortfall }} Video
                            </x-atoms.typography>
                        </div>
                    </div>
                    <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                        {{ $currentVideos }} Video
                    </x-atoms.typography>
                </x-molecules.card>

                <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin-bottom: 24px;">

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
                    @foreach($topLeaders as $leader)
                        @php
                            $isTop3 = $leader->rank <= 3;
                            $isMe = $leader->user_id === auth()->user()->id;
                        @endphp
                        <x-molecules.card style="padding: 16px; border: {{ $isMe ? '2px solid #333' : '1px solid #e2e8f0' }}; border-radius: 8px; box-shadow: none; background: {{ $isMe ? '#f8fafc' : '#ffffff' }}; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; border: {{ $isTop3 ? 'none' : '1px solid #cbd5e1' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; color: {{ $isTop3 ? '#fff' : '#333' }}; background: {{ $isTop3 ? '#333' : 'transparent' }};">
                                    {{ $leader->rank }}
                                </div>
                                <x-atoms.typography variant="body" style="font-weight: 800; font-size: 15px; color: var(--text-primary); display: block;">
                                    {{ '@' . $leader->user->username }} {{ $isMe ? '(Anda)' : '' }}
                                </x-atoms.typography>
                            </div>
                            <x-atoms.typography variant="h4" style="font-weight: 800; font-size: 15px; color: var(--text-primary);">
                                {{ $leader->total_videos }} Video
                            </x-atoms.typography>
                        </x-molecules.card>
                    @endforeach
                </div>

            @else
                <div style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
                    Belum ada tantangan aktif.
                </div>
            @endif
        @endif

    </div>
</x-organisms.mobile-page-wrapper>
@endsection