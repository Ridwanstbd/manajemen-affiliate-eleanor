<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akuntansi Pribadi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Inter', sans-serif; background-color: #f8fafc; padding: 30px; color: #1e293b;">

    <div style="max-width: 1000px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="margin: 0; font-weight: 700; color: #0f172a;">Dompet Saya</h2>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('dashboard') }}" style="text-decoration: none; padding: 10px 18px; background: white; color: #64748b; border: 1px solid #e2e8f0; border-radius: 10px; font-weight: 500; font-size: 14px;">Kembali</a>
                <a href="{{ route('transactions.transfer') }}" style="text-decoration: none; padding: 10px 18px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; font-weight: 500; font-size: 14px;">⇄ Transfer</a>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 40px; overflow-x: auto; padding-bottom: 10px;">
            @foreach($pockets as $pocket)
            <div style="flex: 0 0 220px; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                <div style="color: #64748b; font-size: 13px; font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $pocket->name }}</div>
                <div style="font-size: 1.4em; font-weight: 700; color: {{ $pocket->balance >= 0 ? '#10b981' : '#ef4444' }}">
                    Rp {{ number_format($pocket->balance, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>

        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; background: #fff;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Riwayat Transaksi</h3>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase;">
                        <th style="padding: 15px 25px;">Tanggal</th>
                        <th style="padding: 15px 25px;">Keterangan</th>
                        <th style="padding: 15px 25px;">Kantong</th>
                        <th style="padding: 15px 25px; text-align: right;">Jumlah</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @forelse($transactions as $trx)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px 25px; color: #94a3b8;">{{ $trx->date->format('d M Y') }}</td>
                        <td style="padding: 15px 25px; font-weight: 500;">{{ $trx->description }}</td>
                        <td style="padding: 15px 25px;"><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px;">{{ $trx->account->name }}</span></td>
                        <td style="padding: 15px 25px; text-align: right; font-weight: 600; color: {{ $trx->type == 'debit' ? '#10b981' : '#ef4444' }}">
                            {{ $trx->type == 'debit' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 40px; text-align: center; color: #94a3b8;">Belum ada transaksi bulan ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>