@if($candidates->count() > 0)
<div class="table-responsive">
    <table class="dataTable" style="width: 100%; text-align: left; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border);">Kreator</th>
                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border);">{{ $metricLabel }}</th>
                <th style="padding: 12px; border-bottom: 1px solid var(--glass-border); text-align: right; width: 300px;">Tetapkan Sebagai Pemenang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($candidates as $candidate)
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <x-atoms.avatar />
                        <strong>{{ $candidate->user->username }}</strong>
                    </div>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); color: var(--primary-blue); font-weight: 700;">
                    {{ $isCurrency ? 'Rp ' . number_format($candidate->$metricValue, 0, ',', '.') : number_format($candidate->$metricValue, 0, ',', '.') }}
                </td>
                <td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: right;">
                    <form action="{{ route('admin-dashboard.challenge-winner.store', $challenge->id) }}" method="POST" style="display: flex; gap: 8px; justify-content: flex-end;">
                        @csrf
                        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                        <input type="hidden" name="user_id" value="{{ $candidate->user_id }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        
                        <select name="reward_given" class="form-control" style="width: 200px; height: 32px; padding: 0 10px; font-size: 12px;" required>
                            <option value="">-- Pilih Hadiah --</option>
                            @foreach($challenge->rewards as $reward)
                                <option value="{{ $reward->reward_description }}">{{ $reward->reward_description }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" style="padding: 0 14px; height: 32px;">Pilih</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div style="text-align: center; padding: 40px; color: var(--text-tertiary); font-size: 14px;">
    Belum ada data metrik/kandidat yang terkumpul untuk periode challenge ini.
</div>
@endif