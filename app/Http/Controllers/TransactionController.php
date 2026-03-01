<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $pockets = DB::table('accounts')
            ->where('type', 'asset')
            ->leftJoin('journal_details', 'accounts.id', '=', 'journal_details.account_id')
            ->selectRaw('accounts.name, COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->groupBy('accounts.id', 'accounts.name')
            ->get();

        $transactions = Journal::with('details.account')
        ->orderBy('date', 'desc')
        ->get();

        return view('transactions.index', compact('transactions', 'pockets'));
    }

    public function create()
    {
        $pockets = Account::where('type', 'asset')->get();
        $categories = Account::where('type', '!=', 'asset')->get();

        return view('transactions.create', compact('pockets', 'categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'pocket_id' => 'required|exists:accounts,id', // Kantong yang dipakai
            'account_id' => 'required|exists:accounts,id', // Kategori transaksi
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $category = Account::find($request->account_id);
            $pocketId = $request->pocket_id;
            $amount = $request->amount;
            
            $journal = Journal::create([
                'date' => $request->date,
                'description' => $category->name, 
            ]);

            // Terjemahan Akuntansi Dinamis berdasarkan Kantong yang dipilih
            if (in_array($category->type, ['revenue', 'liability', 'equity'])) {
                // UANG MASUK (Kantong Debit, Kategori Kredit)
                $journal->details()->create(['account_id' => $pocketId, 'debit' => $amount, 'credit' => 0]);
                $journal->details()->create(['account_id' => $category->id, 'debit' => 0, 'credit' => $amount]);
            } else {
                // UANG KELUAR (Kategori Debit, Kantong Kredit)
                $journal->details()->create(['account_id' => $category->id, 'debit' => $amount, 'credit' => 0]);
                $journal->details()->create(['account_id' => $pocketId, 'debit' => 0, 'credit' => $amount]);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function transfer()
    {
        // Hanya ambil akun yang bertipe Asset (Kantong)
        $pockets = Account::where('type', 'asset')->get();
        return view('transactions.transfer', compact('pockets'));
    }

    // Memproses logika akuntansi pemindahan dana
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'from_pocket_id' => 'required|exists:accounts,id|different:to_pocket_id', // Tidak boleh transfer ke kantong yang sama
            'to_pocket_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255'
        ], [
            'from_pocket_id.different' => 'Kantong tujuan tidak boleh sama dengan kantong sumber.'
        ]);

        DB::transaction(function () use ($request) {
            // Jika keterangan kosong, isi dengan default
            $desc = $request->description ?: 'Transfer Antar Kantong';

            // 1. Buat Header Jurnal
            $journal = Journal::create([
                'date' => $request->date,
                'description' => $desc,
            ]);

            $amount = $request->amount;

            // 2. Terjemahan Akuntansi Transfer (Sesama Aset)
            // KANTONG TUJUAN BERTAMBAH (Debit)
            $journal->details()->create([
                'account_id' => $request->to_pocket_id, 
                'debit' => $amount, 
                'credit' => 0
            ]);

            // KANTONG SUMBER BERKURANG (Kredit)
            $journal->details()->create([
                'account_id' => $request->from_pocket_id, 
                'debit' => 0, 
                'credit' => $amount
            ]);
        });

        return redirect()->route('transactions.index')->with('success', 'Transfer antar kantong berhasil!');
    }
}
