<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Transaksi Baru</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; padding: 40px 20px; margin: 0; }
        .card { max-width: 500px; margin: 0 auto; background: white; padding: 35px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; font-weight: 700; color: #0f172a; text-align: center; margin-bottom: 25px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px; }
        
        input, select {
            width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 10px;
            font-size: 15px; transition: all 0.3s ease; box-sizing: border-box; font-family: inherit;
        }
        input:focus, select:focus { outline: none; border-color: #2563eb; ring: 3px rgba(37, 99, 235, 0.1); }
        
        .btn-submit {
            width: 100%; padding: 14px; background-color: #2563eb; color: white; border: none;
            border-radius: 10px; font-weight: 600; font-size: 16px; cursor: pointer;
            margin-top: 10px; transition: background 0.3s ease;
        }
        .btn-submit:hover { background-color: #1d4ed8; }
        
        .btn-back {
            display: block; text-align: center; margin-top: 20px; text-decoration: none;
            color: #94a3b8; font-size: 14px; font-weight: 500;
        }
        .btn-back:hover { color: #64748b; }

        /* Styling khusus untuk input nominal agar terlihat menonjol */
        .input-amount { font-size: 1.3em; font-weight: 800; text-align: center; color: #0f172a; border: 2px solid #e8e9bb !important; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Catat Transaksi</h2>

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}">
            </div>

            <div class="form-group">
                <label>Gunakan Kantong</label>
                <select name="pocket_id" required>
                    <option value="" disabled selected>-- Pilih Sumber / Tujuan Dana --</option>
                    @foreach($pockets as $pocket)
                        <option value="{{ $pocket->id }}">{{ $pocket->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Jenis Transaksi</label>
                <select id="typeSelect" required onchange="updateCategory()">
                    <option value="" disabled selected>-- Pilih Kelompok --</option>
                    <option value="revenue" style="color: #10b981;">🟢 Pendapatan (Uang Masuk)</option>
                    <option value="expense" style="color: #ef4444;">🔴 Beban (Uang Keluar)</option>
                    <option value="liability">🏦 Kewajiban (Utang)</option>
                    <option value="equity">💎 Ekuitas (Modal)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Kategori Akun</label>
                <select name="account_id" id="categorySelect" required>
                    <option value="" disabled selected>-- Pilih Jenis Terlebih Dahulu --</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nominal (Rp)</label>
                <input type="number" name="amount" min="1" required placeholder="0" class="input-amount">
            </div>
            
            <button type="submit" class="btn-submit">Simpan Transaksi</button>
        </form>

        <a href="{{ route('transactions.index') }}" class="btn-back">
            ← Batal dan Kembali
        </a>
    </div>

    <script>
        // Data kategori (selain kantong/asset)
        const categories = @json($categories);

        function updateCategory() {
            const selectedType = document.getElementById('typeSelect').value;
            const categorySelect = document.getElementById('categorySelect');
            
            categorySelect.innerHTML = '<option value="" disabled selected>-- Pilih Kategori --</option>';
            
            const filteredAccounts = categories.filter(account => account.type === selectedType);
            
            filteredAccounts.forEach(account => {
                let opt = document.createElement('option');
                opt.value = account.id;
                opt.innerHTML = account.name;
                categorySelect.appendChild(opt);
            });
            
            // Tambahkan efek fokus otomatis ke select kategori setelah jenis dipilih
            categorySelect.focus();
        }
    </script>
</body>
</html>