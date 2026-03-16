<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\StoreTransferRequest;
use App\Services\TransactionService;
use App\Models\Account;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $pockets = $this->transactionService->getPocketsWithBalance();
        $transactions = $this->transactionService->getFormattedTransactions();

        return view('transactions.index', compact('transactions', 'pockets'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $this->transactionService->createTransaction($request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function transfer()
    {
        $pockets = Account::where('type', 'asset')->get();
        return view('transactions.transfer', compact('pockets'));
    }

    public function storeTransfer(StoreTransferRequest $request)
    {
        $this->transactionService->createTransfer($request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transfer antar kantong berhasil!');
    }
}