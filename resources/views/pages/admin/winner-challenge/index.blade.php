@extends('layouts.app')
@section('title', 'Kelola Pemenang Challenge')

@section('content')
<div class="content">
    
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('admin-dashboard.challenge.index') }}" class="btn btn-secondary btn-sm" style="padding: 8px 12px;">
            <x-atoms.icon name="chevron-left" style="width: 16px; height: 16px;" /> Kembali
        </a>
        <div>
            <x-atoms.typography variant="card-title" style="margin: 0; font-size: 20px;">Kelola Pemenang: {{ $challenge->title }}</x-atoms.typography>
            <x-atoms.typography variant="card-subtitle" style="margin: 0; font-size: 13px;">Periode: {{ \Carbon\Carbon::parse($challenge->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($challenge->end_date)->translatedFormat('d M Y') }}</x-atoms.typography>
        </div>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: 1fr; gap: 24px;">
        
        <x-molecules.card title="Daftar Pemenang Terpilih" description="Kreator yang telah ditetapkan sebagai pemenang untuk challenge ini.">
            @if($challenge->winners->count() > 0)
                <div class="table-responsive">
                    <table class="dataTable" style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border);">Kreator</th>
                                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border);">Kategori Menang</th>
                                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border);">Hadiah Diberikan</th>
                                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border); text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($challenge->winners as $winner)
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <strong>{{ $winner->user->username }}</strong>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <x-atoms.badge variant="success">{{ $winner->category }}</x-atoms.badge>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">{{ $winner->reward_given }}</td>
                                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: right;">
                                    <form action="{{ route('admin-dashboard.challenge-winner.destroy', ['challenge' => $challenge->id, 'winner' => $winner->id]) }}" method="POST" onsubmit="return confirm('Batalkan kemenangan kreator ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $winner->id }}">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 6px 10px; font-size: 12px;">Batalkan</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 30px; color: var(--text-tertiary); font-size: 14px;">
                    Belum ada pemenang yang ditetapkan.
                </div>
            @endif
        </x-molecules.card>

        <x-molecules.card title="Tentukan Pemenang Baru" description="Pilih dari daftar rekomendasi sistem atau input secara manual.">
            
            <div class="custom-tabs" style="display: flex; gap: 8px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px; margin-bottom: 20px;">
                <button type="button" class="btn btn-secondary tab-btn active" data-target="tab-gmv" style="border: none; background: var(--primary-blue-soft); color: var(--primary-blue);">
                <x-atoms.icon name="revenue" style="width: 14px; height: 14px;" />Top GMV</button>
                <button type="button" class="btn btn-secondary tab-btn" data-target="tab-video" style="border: none;"><x-atoms.icon name="video" style="width: 14px; height: 14px;" />Top Video</button>
                <button type="button" class="btn btn-secondary tab-btn" data-target="tab-manual" style="border: none;"> <x-atoms.icon name="eye" style="width: 14px; height: 14px;" />Manual (Views)</button>
            </div>

            <div id="tab-gmv" class="tab-pane" style="display: block;">
                @include('pages.admin.winner-challenge.partials.table-candidates', [
                    'candidates' => $topGmv,
                    'category' => 'GMV TERTINGGI',
                    'metricLabel' => 'Total GMV',
                    'metricValue' => 'total_gmv',
                    'isCurrency' => true
                ])
            </div>

            <div id="tab-video" class="tab-pane" style="display: none;">
                @include('pages.admin.winner-challenge.partials.table-candidates', [
                    'candidates' => $topVideos,
                    'category' => 'VIDEO TERBANYAK',
                    'metricLabel' => 'Jumlah Video',
                    'metricValue' => 'total_videos',
                    'isCurrency' => false
                ])
            </div>

            <div id="tab-manual" class="tab-pane" style="display: none;">
                <form action="{{ route('admin-dashboard.challenge-winner.store', $challenge->id) }}" method="POST" style=" background: rgba(0,0,0,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border);">
                    @csrf
                    <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                    <input type="hidden" name="category" value="PERFORMA VIEWS">
                    
                    <div style="margin-bottom: 16px;">
                        <x-atoms.searchable-select 
                            name="user_id" 
                            label="Pilih Kreator"
                            placeholder="-- Cari username atau email affiliator --"
                            :options="$affiliators->map(fn($aff) => [
                                'id' => $aff->id, 
                                'text' => $aff->username, 
                                'subtext' => $aff->email
                            ])"
                            required
                        />
                    </div>

                    <div style="margin-bottom: 24px;">
                        <x-atoms.searchable-select 
                            name="reward_given" 
                            label="Pilih Hadiah"
                            placeholder="-- Cari dan Pilih Hadiah --"
                            :options="$challenge->rewards->map(fn($reward) => [
                                'id' => $reward->reward_description, 
                                'text' => $reward->reward_description, 
                                'subtext' => 'Target: ' . $reward->target_value
                            ])"
                            required
                        />
                    </div>

                    <x-atoms.button type="submit" variant="primary" style="width: 100%;">Tetapkan Pemenang Views</x-atoms.button>
                </form>
            </div>
            
        </x-molecules.card>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.background = 'transparent';
                b.style.color = 'var(--text-secondary)';
            });
            
            this.style.background = 'var(--primary-blue-soft)';
            this.style.color = 'var(--primary-blue)';
            
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.style.display = 'none';
            });
            
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).style.display = 'block';
        });
    });
</script>
@endpush