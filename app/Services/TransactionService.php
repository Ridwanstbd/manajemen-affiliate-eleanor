<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function getPocketsWithBalance()
    {
        return DB::table('accounts')
            ->where('type', 'asset')
            ->leftJoin('journal_details', 'accounts.id', '=', 'journal_details.account_id')
            ->selectRaw('accounts.name, COALESCE(SUM(debit) - SUM(credit), 0) as balance')
            ->groupBy('accounts.id', 'accounts.name')
            ->get();
    }

    public function getFormattedTransactions()
    {
        $transactionDetails = JournalDetail::with(['journal', 'account'])
            ->whereHas('account', function ($q) {
                $q->where('type', 'asset');
            })
            ->get();

        return $transactionDetails->map(function ($detail) {
            return (object) [
                'date' => $detail->journal->date,
                'description' => $detail->journal->description,
                'account' => (object) ['name' => $detail->account->name],
                'type' => $detail->debit > 0 ? 'debit' : 'credit',
                'amount' => $detail->debit > 0 ? $detail->debit : $detail->credit,
            ];
        })->sortByDesc('date');
    }

    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $category = Account::find($data['account_id']);
            $pocketId = $data['pocket_id'];
            $amount = $data['amount'];

            $journal = Journal::create([
                'date' => $data['date'],
                'description' => $category->name,
            ]);

            if (in_array($category->type, ['revenue', 'liability', 'equity'])) {
                $journal->details()->create(['account_id' => $pocketId, 'debit' => $amount, 'credit' => 0]);
                $journal->details()->create(['account_id' => $category->id, 'debit' => 0, 'credit' => $amount]);
            } else {
                $journal->details()->create(['account_id' => $category->id, 'debit' => $amount, 'credit' => 0]);
                $journal->details()->create(['account_id' => $pocketId, 'debit' => 0, 'credit' => $amount]);
            }

            return $journal;
        });
    }

    public function createTransfer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $desc = $data['description'] ?: 'Transfer Antar Kantong';
            $amount = $data['amount'];

            $journal = Journal::create([
                'date' => $data['date'],
                'description' => $desc,
            ]);

            $journal->details()->create([
                'account_id' => $data['to_pocket_id'],
                'debit' => $amount,
                'credit' => 0
            ]);

            $journal->details()->create([
                'account_id' => $data['from_pocket_id'],
                'debit' => 0,
                'credit' => $amount
            ]);

            return $journal;
        });
    }
}