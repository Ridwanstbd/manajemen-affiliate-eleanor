<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Inter', sans-serif; padding: 40px 20px; background-color: #f0f4f8; color: #334155;">

    <div style="max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center;">
        
        <p style="text-transform: uppercase; letter-spacing: 1px; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 10px;">
            Total Saldo Seluruh Aset
        </p>
        
        <h1 style="color: #0f172a; font-size: 3.5em; margin: 0 0 30px 0; font-weight: 700;">
            <span style="font-size: 0.5em; vertical-align: middle; color: #94a3b8; font-weight: 400;">Rp</span> {{ number_format($totalAssets, 0, ',', '.') }}
        </h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 35px;">
            <div style="background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #64748b; font-size: 14px;">Aset di Tangan</p>
                <h3 style="margin: 10px 0 0 0; color: #0ea5e9; font-size: 1.2em;">Rp {{ number_format($assetDiTangan, 0, ',', '.') }}</h3>
            </div>

            <div style="background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #64748b; font-size: 14px;">Aset di Orang (Piutang)</p>
                <h3 style="margin: 10px 0 0 0; color: #f59e0b; font-size: 1.2em;">Rp {{ number_format($assetDiOrang, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div style="background-color: #f1f5f9; padding: 15px; border-radius: 12px; margin-bottom: 40px;">
            <p style="color: #475569; font-size: 14px; margin: 0; line-height: 1.6;">
                💡 Ini adalah gabungan dari seluruh kantong utama, tabungan, dan dana darurat Anda.
            </p>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('transactions.index') }}" style="text-decoration: none; flex: 1; min-width: 200px;">
                <div style="padding: 15px; background-color: #0f172a; color: white; border-radius: 12px; font-weight: 600; transition: 0.3s; cursor: pointer;">
                    📊 Detail Transaksi
                </div>
            </a>

            <a href="{{ route('transactions.create') }}" style="text-decoration: none; flex: 1; min-width: 200px;">
                <div style="padding: 15px; background-color: #22c55e; color: white; border-radius: 12px; font-weight: 600; transition: 0.3s; cursor: pointer;">
                    + Tambah Transaksi
                </div>
            </a>
        </div>

    </div>

</body>
</html>