<?php

namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $previousMonth = Carbon::now()->subMonth()->month;
        $previousYear = Carbon::now()->subMonth()->year;

        $revenueCurrent = $this->getSummary('revenue', $currentMonth, $currentYear);
        $revenuePrevious = $this->getSummary('revenue', $previousMonth, $previousYear);
        
        $expenseCurrent = $this->getSummary('expense', $currentMonth, $currentYear);
        $expensePrevious = $this->getSummary('expense', $previousMonth, $previousYear);
        
        $profitCurrent = $revenueCurrent - $expenseCurrent;
        $profitPrevious = $revenuePrevious - $expensePrevious;
        
        $cashCurrent = $this->getSummary('asset', $currentMonth, $currentYear, true);
        $cashPrevious = $this->getSummary('asset', $previousMonth, $previousYear, true);

        return [
            'totalRevenue' => $revenueCurrent,
            'revenueTrend' => $this->calculateTrend($revenueCurrent, $revenuePrevious),
            'totalExpenses' => $expenseCurrent,
            'expenseTrend' => $this->calculateTrend($expenseCurrent, $expensePrevious),
            'netProfit' => $profitCurrent,
            'profitTrend' => $this->calculateTrend($profitCurrent, $profitPrevious),
            'cashOnHand' => $cashCurrent,
            'cashTrend' => $this->calculateTrend($cashCurrent, $cashPrevious),
            'pockets' => Account::where('type', 'asset')->get(),
            'categories' => Account::where('type', '!=', 'asset')->get()
        ];
    }

    private function getSummary($type, $month, $year, $isCash = false)
    {
        $query = DB::table('journal_details')
            ->join('accounts', 'journal_details.account_id', '=', 'accounts.id')
            ->whereMonth('journal_details.created_at', $month)
            ->whereYear('journal_details.created_at', $year)
            ->where('accounts.type', $type);

        if ($isCash) {
            $query->where(function ($q) {
                $q->where('accounts.name', 'like', '%Kas%')
                  ->orWhere('accounts.name', 'like', '%Bank%')
                  ->orWhere('accounts.name', 'like', '%Kantong%');
            });
        }

        if ($type === 'revenue') {
            $query->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as total');
        } else {
            $query->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total');
        }

        return $query->value('total') ?? 0;
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / abs($previous)) * 100;
    }
}