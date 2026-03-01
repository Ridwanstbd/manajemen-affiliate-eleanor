<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; // Gunakan facade yang lengkap
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function index()
    {
        $totalAssets = DB::table('journal_details')
            ->join('accounts', 'journal_details.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'asset')
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
            ->value('total') ?? 0;

        $assetDiTangan = DB::table('journal_details')
            ->join('accounts', 'journal_details.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'asset')
            ->where('name', 'like', '%Kantong%') // Filter kategori kas/bank
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
            ->value('total') ?? 0;

        $assetDiOrang = DB::table('journal_details')
            ->join('accounts', 'journal_details.account_id', '=', 'accounts.id')
            ->where('accounts.type', 'asset')
            ->where('name', 'like', '%Piutang%') // Filter kategori kas/bank
            ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
            ->value('total') ?? 0;

        return view('index', compact('totalAssets', 'assetDiTangan', 'assetDiOrang'));
    }
}