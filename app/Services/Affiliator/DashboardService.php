<?php

namespace App\Services\Affiliator;

use App\Models\Challenge;
use App\Models\CreatorMetric;
use App\Models\ImportHistory;
use App\Models\SampleRequest;
use App\Models\TaskReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardStats()
    {
        $userId = Auth::id();

        $latestImport = ImportHistory::max('start_date');

        if ($latestImport) {
            $latestDate = Carbon::parse($latestImport);
            $currentMonth = $latestDate->month;
            $currentYear = $latestDate->year;
        } else {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
        }

        $previousDate = Carbon::create($currentYear, $currentMonth, 1)->subMonth();
        $previousMonth = $previousDate->month;
        $previousYear = $previousDate->year;

        $currentMetrics = $this->getMonthlyMetrics($userId, $currentMonth, $currentYear);
        $previousMetrics = $this->getMonthlyMetrics($userId, $previousMonth, $previousYear);

        $currGmv  = (float) ($currentMetrics->gmv ?? 0);
        $prevGmv  = (float) ($previousMetrics->gmv ?? 0);
        $currItems = (int) ($currentMetrics->items ?? 0);
        $prevItems = (int) ($previousMetrics->items ?? 0);
        $currComm = (float) ($currentMetrics->commission ?? 0);
        $prevComm = (float) ($previousMetrics->commission ?? 0);
        $currRef  = (float) ($currentMetrics->refunds ?? 0);
        $prevRef  = (float) ($previousMetrics->refunds ?? 0);

        $gmvTrend = $this->calculateTrend($currGmv, $prevGmv);
        $itemsTrend = $this->calculateTrend($currItems, $prevItems);
        $commissionTrend = $this->calculateTrend($currComm, $prevComm);
        $refundTrend = $this->calculateTrend($currRef, $prevRef);

        $chartDataRaw = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = isset($latestDate) ? $latestDate->copy()->subMonths($i) : Carbon::now()->subMonths($i);
            $metrics = $this->getMonthlyMetrics($userId, $date->month, $date->year);
            $chartDataRaw[] = [
                'bulan' => $date->translatedFormat('M'),
                'gmv'   => (float) $metrics->gmv,
                'items' => (int) $metrics->items,
            ];
        }

        $maxGmv = max(array_column($chartDataRaw, 'gmv')) ?: 1;
        $maxItems = max(array_column($chartDataRaw, 'items')) ?: 1;

        $chartData = array_map(function($item) use ($maxGmv, $maxItems) {
            return [
                'bulan'         => $item['bulan'],
                'gmv_percent'   => ($item['gmv'] / $maxGmv) * 100,
                'gmv'           => $item['gmv'],
                'items'         => $item['items'],
                'items_percent' => ($item['items'] / $maxItems) * 100,
            ];
        }, $chartDataRaw);

        $sampleStatuses = SampleRequest::where('user_id', $userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $pendingTasks = TaskReport::with('products') 
            ->where('user_id', $userId)
            ->where('task_status', '!=', 'COMPLETED')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->get();
        $activeChallenges = Challenge::where('is_active', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->latest()
            ->get();
        return [
            'stats' => [
                'gmv' => [
                    'value' => $currGmv,
                    'trend' => abs($gmvTrend),
                    'dir'   => $gmvTrend >= 0 ? 'up' : 'down'
                ],
                'items' => [
                    'value' => $currItems,
                    'trend' => abs($itemsTrend),
                    'dir'   => $itemsTrend >= 0 ? 'up' : 'down'
                ],
                'commission' => [
                    'value' => $currComm,
                    'trend' => abs($commissionTrend),
                    'dir'   => $commissionTrend >= 0 ? 'up' : 'down'
                ],
                'refunds' => [
                    'value' => $currRef,
                    'trend' => abs($refundTrend),
                    'dir'   => $refundTrend >= 0 ? 'up' : 'down'
                ],
            ],
            'chartData' => $chartData,
            'sampleSummary' => [
                'pending'  => $sampleStatuses['PENDING'] ?? 0,
                'approved' => $sampleStatuses['APPROVED'] ?? 0,
                'shipped'  => $sampleStatuses['SHIPPED'] ?? 0,
                'rejected' => $sampleStatuses['REJECTED'] ?? 0,
            ],
            'pendingTasks' => $pendingTasks,
            'activeChallenges' => $activeChallenges
        ];
    }

    private function getMonthlyMetrics($userId, $month, $year)
    {
        $result = CreatorMetric::where('user_id', $userId)
            ->whereHas('importHistory', function($q) use($month, $year) {
                $q->whereMonth('start_date', $month)
                  ->whereYear('start_date', $year);
            })->selectRaw('
                COALESCE(SUM(affiliate_gmv), 0) as gmv, 
                COALESCE(SUM(items_sold), 0) as items, 
                COALESCE(SUM(estimated_commission), 0) as commission, 
                COALESCE(SUM(refunds), 0) as refunds
            ')->first();

        if (!$result) {
            return (object) [
                'gmv' => 0,
                'items' => 0,
                'commission' => 0,
                'refunds' => 0,
            ];
        }

        return $result;
    }
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / abs($previous)) * 100;
    }
}