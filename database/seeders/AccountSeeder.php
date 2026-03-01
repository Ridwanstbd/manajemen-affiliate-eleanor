<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    public function run()
    {
        // 1. ASSET = KANTONG (Dompet / Rekening)
        Account::create(['code' => '101', 'name' => 'Kantong Rekening', 'type' => 'asset']);
        Account::create(['code' => '102', 'name' => 'Kantong Jajan & Makan', 'type' => 'asset']);
        Account::create(['code' => '103', 'name' => 'Kantong Dompet', 'type' => 'asset']);
        Account::create(['code' => '104', 'name' => 'Kantong Tabungan', 'type' => 'asset']);
        Account::create(['code' => '105', 'name' => 'Kantong Dana Darurat', 'type' => 'asset']);
        Account::create(['code' => '106', 'name' => 'Piutang (Uang di Orang)', 'type' => 'asset']);

        // 2. LIABILITY (Utang)
        Account::create(['code' => '201', 'name' => 'Pinjaman Bank', 'type' => 'liability']);

        // 3. EQUITY (Modal)
        Account::create(['code' => '301', 'name' => 'Modal Awal', 'type' => 'equity']);

        // 4. REVENUE (Pendapatan)
        Account::create(['code' => '401', 'name' => 'Gaji Bulanan', 'type' => 'revenue']);
        Account::create(['code' => '402', 'name' => 'Bonus / Freelance', 'type' => 'revenue']);
        Account::create(['code' => '403', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue']);

        // 5. EXPENSE (Beban / Kategori Pengeluaran)
        Account::create(['code' => '501', 'name' => 'Makan & Minum', 'type' => 'expense']);
        Account::create(['code' => '502', 'name' => 'Bensin / Ojol', 'type' => 'expense']);
        Account::create(['code' => '503', 'name' => 'Nonton / Hiburan', 'type' => 'expense']);
        Account::create(['code' => '504', 'name' => 'Buku /Pendidikan', 'type' => 'expense']);
        Account::create(['code' => '505', 'name' => 'Cicilan / Bayar Utang', 'type' => 'expense']);

    }
}