<?php

namespace App\Services\Admin;

use App\Models\CoreMetric;
use App\Models\CreatorMetric;
use App\Models\ImportHistory;
use App\Models\SampleRequest;
use App\Models\SystemAccessRequest;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardStats()
    {
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

        $currentMetrics = $this->getMonthlyCoreMetrics($currentMonth, $currentYear);
        $previousMetrics = $this->getMonthlyCoreMetrics($previousMonth, $previousYear);

        $currGmv = (float) ($currentMetrics->gmv ?? 0);
        $prevGmv = (float) ($previousMetrics->gmv ?? 0);
        
        $currItems = (int) ($currentMetrics->items ?? 0);
        $prevItems = (int) ($previousMetrics->items ?? 0);
        
        $currComm = (float) ($currentMetrics->commission ?? 0);
        $prevComm = (float) ($previousMetrics->commission ?? 0);
        
        $currRef = (float) ($currentMetrics->refunds ?? 0);
        $prevRef = (float) ($previousMetrics->refunds ?? 0);

        $gmvTrend = $this->calculateTrend($currGmv, $prevGmv);
        $itemsTrend = $this->calculateTrend($currItems, $prevItems);
        $commissionTrend = $this->calculateTrend($currComm, $prevComm);
        $refundTrend = $this->calculateTrend($currRef, $prevRef);

        $chartDataRaw = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = isset($latestDate) ? $latestDate->copy()->subMonths($i) : Carbon::now()->subMonths($i);
            $metrics = $this->getMonthlyCoreMetrics($date->month, $date->year);
            $chartDataRaw[] = [
                'bulan' => $date->translatedFormat('M'),
                'gmv' => (float) $metrics->gmv,
                'items' => (int) $metrics->items,
            ];
        }

        $maxGmv = max(array_column($chartDataRaw, 'gmv')) ?: 1;
        $maxItems = max(array_column($chartDataRaw, 'items')) ?: 1;

        $chartData = array_map(function($item) use ($maxGmv, $maxItems) {
            return [
                'bulan' => $item['bulan'],
                'gmv_percent' => ($item['gmv'] / $maxGmv) * 100,
                'gmv'           => $item['gmv'],
                'items'         => $item['items'],
                'items_percent' => ($item['items'] / $maxItems) * 100,
            ];
        }, $chartDataRaw);

        $sampleStatuses = SampleRequest::selectRaw('status, count(*) as count')->groupBy('status')->get();
        
        $statusColors = [
            'PENDING'  => '#f59e0b',
            'APPROVED' => 'var(--primary-blue)',
            'SHIPPED'  => 'var(--emerald)',
            'REJECTED' => '#ef4444'
        ];
        
        $sampleStatusData = [];
        foreach ($sampleStatuses as $status) {
            $sampleStatusData[] = [
                'label' => ucfirst(strtolower($status->status)),
                'value' => $status->count,
                'color' => $statusColors[$status->status] ?? '#94a3b8'
            ];
        }

        if (empty($sampleStatusData)) {
            $sampleStatusData = [['label' => 'Belum ada data', 'value' => 1, 'color' => '#e2e8f0']];
        }

        $topCreators = CreatorMetric::with('user')
            ->selectRaw('user_id, SUM(affiliate_gmv) as total_gmv, SUM(items_sold) as total_items, SUM(estimated_commission) as total_commission')
            ->groupBy('user_id')
            ->orderByDesc('total_gmv')
            ->limit(5)
            ->get();

        $tasks = collect();

        $accessRequests = SystemAccessRequest::where('status', 'PENDING')->latest()->take(3)->get();
        foreach ($accessRequests as $req) {
            $tasks->push((object)[
                'title' => 'Tinjau Pengajuan Akses Baru',
                'name'  => $req->tiktok_username ?? $req->email,
                'time'  => $req->created_at->diffForHumans(),
                'created_at' => $req->created_at,
                'route' => route('admin-dashboard.users.index')
            ]);
        }

        $sampleRequests = SampleRequest::with('user')->where('status', 'PENDING')->latest()->take(3)->get();
        foreach ($sampleRequests as $req) {
            $tasks->push((object)[
                'title' => 'Tinjau Pengajuan Sampel Baru',
                'name'  => $req->user->username ?? 'Kreator',
                'time'  => $req->created_at->diffForHumans(),
                'created_at' => $req->created_at,
                'route' => route('admin-dashboard.request-samples.index')
            ]);
        }

        $pendingTasksList = $tasks->sortByDesc('created_at')->take(4)->values();

        return [
            'stats' => [
                'gmv' => [
                    'value' => $currGmv,
                    'trend' => abs($gmvTrend),
                    'dir' => $gmvTrend >= 0 ? 'up' : 'down'
                ],
                'items' => [
                    'value' => $currItems,
                    'trend' => abs($itemsTrend),
                    'dir' => $itemsTrend >= 0 ? 'up' : 'down'
                ],
                'commission' => [
                    'value' => $currComm,
                    'trend' => abs($commissionTrend),
                    'dir' => $commissionTrend >= 0 ? 'up' : 'down'
                ],
                'refunds' => [
                    'value' => $currRef,
                    'trend' => abs($refundTrend),
                    'dir' => $refundTrend >= 0 ? 'up' : 'down'
                ],
            ],
            'chartData' => $chartData,
            'sampleStatusData' => $sampleStatusData,
            'topCreators' => $topCreators,
            'pendingTasksList' => $pendingTasksList
        ];
    }

    private function getMonthlyCoreMetrics($month, $year)
    {
        $result = CoreMetric::whereHas('importHistory', function($q) use($month, $year) {
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