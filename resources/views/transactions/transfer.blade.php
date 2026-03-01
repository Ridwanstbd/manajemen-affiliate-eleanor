<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Antar Kantong</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; color: #1e293b; padding: 40px 20px; margin: 0; }
        .card { max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 30px; letter-spacing: -0.5px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        input, select {
            width: 100%; padding: 14px; border: 1.5px solid #e2e8f0; border-radius: 12px;
            font-size: 15px; transition: all 0.2s ease; box-sizing: border-box; font-family: inherit;
        }
        input:focus, select:focus { outline: none; border-color: #10b981; background-color: #f0fdf4; }

        /* Style khusus untuk alur transfer */
        .transfer-flow { background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 25px; border: 1px dashed #cbd5e1; position: relative; }
        .transfer-arrow { text-align: center; font-size: 20px; color: #10b981; margin: 10px 0; font-weight: bold; }
        
        .alert-error {
            background-color: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c;
            padding: 15px; border-radius: 12px; margin-bottom: 25px; font-size: 14px;
        }
        .alert-error ul { margin: 0; padding-left: 20px; }

        .btn-submit {
            width: 100%; padding: 16px; background-color: #10b981; color: white; border: none;
            border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3); transition: all 0.3s ease;
        }
        .btn-submit:hover { background-color: #059669; transform: translateY(-1px); }
        
        .btn-back {
            display: block; text-align: center; margin-top: 25px; text-decoration: none;
            color: #94a3b8; font-size: 14px; font-weight: 500;
        }
        .btn-back:hover { color: #64748b; }

        .input-amount { font-size: 1.3em; font-weight: 800; text-align: center; color: #0f172a; border: 2px solid #10b981 !important; }
    </style>
</head>
<body>

    <div class="card">
        <h2>⇄ Transfer Saldo</h2>

        @if($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transactions.storeTransfer') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Tanggal Transfer</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}">
            </div>

            <div class="transfer-flow">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Dari Kantong</label>
                    <select name="from_pocket_id" required>
                        <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                        @foreach($pockets as $pocket)
                            <option value="{{ $pocket->id }}">💰 {{ $pocket->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="transfer-arrow">↓</div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ke Kantong</label>
                    <select name="to_pocket_id" required>
                        <option value="" disabled selected>-- Pilih Tujuan Alokasi --</option>
                        @foreach($pockets as $pocket)
                            <option value="{{ $pocket->id }}">📥 {{ $pocket->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Nominal Transfer</label>
                <input type="number" name="amount" min="1" required placeholder="0" class="input-amount">
            </div>

            <div class="form-group">
                <label>Keterangan (Opsional)</label>
                <input type="text" name="description" placeholder="Misal: Isi saldo tabungan">
            </div>
            
            <button type="submit" class="btn-submit">Konfirmasi Transfer</button>
        </form>

        <a href="{{ route('transactions.index') }}" class="btn-back">
            ← Kembali ke Riwayat
        </a>
    </div>

</body>
</html>