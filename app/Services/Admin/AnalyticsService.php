<?php

namespace App\Services\Admin;

use App\Models\CoreMetric;
use App\Models\CreatorMetric;
use App\Models\ProductMetric;
use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\TaskReport;
use App\Models\ImportHistory;
use App\Models\VideoProductMetric;
use App\Models\LiveProductMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsService
{
    public function getTabData($tab, Request $request)
    {
        switch ($tab) {
            case 'summary':
                return $this->getSummaryData($request);
            case 'detail':
                return $this->getDetailRoiData($request);
            case 'analytics':
            default:
                return $this->getAnalyticsRoiData($request);
        }
    }

    private function getTargetDate(Request $request)
    {
        $selected = $request->input('selected_month');
        if ($selected) {
            return Carbon::parse($selected . '-01');
        }
        $latest = ImportHistory::latest('start_date')->first();
        return $latest ? Carbon::parse($latest->start_date) : Carbon::now();
    }

    private function getAnalyticsRoiData(Request $request)
    {
        $currentMonth = $this->getTargetDate($request);
        $prevMonth = $currentMonth->copy()->subMonth();

        $currCore = $this->getCoreMetrics($currentMonth->month, $currentMonth->year);
        $prevCore = $this->getCoreMetrics($prevMonth->month, $prevMonth->year);

        $currBiayaSampel = SampleRequest::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('shipping_cost') ?? 0;
            
        $prevBiayaSampel = SampleRequest::whereMonth('created_at', $prevMonth->month)
            ->whereYear('created_at', $prevMonth->year)
            ->sum('shipping_cost') ?? 0;

        $currRoi = $currBiayaSampel > 0 ? ($currCore->gmv / $currBiayaSampel) : 0;
        $prevRoi = $prevBiayaSampel > 0 ? ($prevCore->gmv / $prevBiayaSampel) : 0;

        $totalPermintaan = SampleRequest::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        
        $disetujui = SampleRequest::whereIn('status', ['APPROVED', 'SHIPPED'])
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count() + ($currCore->samples ?? 0);
        
        $kontenDibuat = TaskReport::where('task_status', 'COMPLETED')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
            
        $konversi = VideoProductMetric::sum('orders') + LiveProductMetric::sum('orders');

        $funnel = [
            'total' => $totalPermintaan,
            'approved' => $disetujui,
            'approved_pct' => $totalPermintaan > 0 ? round(($disetujui / $totalPermintaan) * 100) : 0,
            'content' => $kontenDibuat,
            'content_pct' => $disetujui > 0 ? round(($kontenDibuat / $disetujui) * 100) : 0,
            'conversion' => $konversi,
        ];

        $topProducts = ProductMetric::with('product')
            ->selectRaw('product_id, SUM(affiliate_gmv) as total_gmv')
            ->groupBy('product_id')
            ->orderByDesc('total_gmv')
            ->limit(3)
            ->get()
            ->map(function($metric) {
                $cost = SampleRequest::whereHas('details', function ($query) use ($metric) {
                    $query->where('product_id', $metric->product_id);
                })->sum('shipping_cost') ?? 0;

                $roi = $cost > 0 ? ($metric->total_gmv / $cost) : 0;
                return [
                    'name' => $metric->product->name ?? 'Produk Unknown',
                    'roi' => number_format($roi, 1) . 'x'
                ];
            });

        $m1 = $request->input('month_1', $prevMonth->format('Y-m'));
        $m2 = $request->input('month_2', $currentMonth->format('Y-m'));
        $date1 = Carbon::parse($m1 . '-01');
        $date2 = Carbon::parse($m2 . '-01');

        $core1 = $this->getCoreMetrics($date1->month, $date1->year);
        $core2 = $this->getCoreMetrics($date2->month, $date2->year);

        $komparasiData = [
            ['kategori' => 'GMV Afiliasi', 'bulan_1' => (float)$core1->gmv, 'bulan_2' => (float)$core2->gmv],
            ['kategori' => 'Brg Terjual', 'bulan_1' => (int)$core1->items, 'bulan_2' => (int)$core2->items],
            ['kategori' => 'Tk. Pengembalian', 'bulan_1' => (float)$core1->refunds, 'bulan_2' => (float)$core2->refunds],
        ];

        return [
            'metrics' => [
                'biaya' => ['val' => $currBiayaSampel, 'trend' => $this->calcTrend($currBiayaSampel, $prevBiayaSampel)],
                'gmv' => ['val' => $currCore->gmv, 'trend' => $this->calcTrend($currCore->gmv, $prevCore->gmv)],
                'roi' => ['val' => $currRoi, 'trend' => $currRoi - $prevRoi],
            ],
            'funnel' => $funnel,
            'topProducts' => $topProducts,
            'komparasiData' => $komparasiData,
            'month1' => $m1,
            'month2' => $m2
        ];
    }

    private function getSummaryData(Request $request)
    {
        $currentMonth = $this->getTargetDate($request);
        $prevMonth = $currentMonth->copy()->subMonth();

        $currCore = $this->getCoreMetrics($currentMonth->month, $currentMonth->year);
        $prevCore = $this->getCoreMetrics($prevMonth->month, $prevMonth->year);
        
        $currSampel = SampleRequest::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count() + $currCore->samples;
                                   
        $prevSampel = SampleRequest::whereMonth('created_at', $prevMonth->month)
            ->whereYear('created_at', $prevMonth->year)
            ->count() + $prevCore->samples;

        $top5 = CreatorMetric::with(['user' => function($q) {
                $q->where('is_kol', false);
            }])
            ->whereHas('user', function($q) {
                $q->where('is_kol', false);
            })
            ->whereHas('importHistory', function($q) use($currentMonth) {
                $q->whereMonth('start_date', $currentMonth->month)
                  ->whereYear('start_date', $currentMonth->year);
            })
            ->selectRaw('user_id, SUM(affiliate_gmv) as gmv, SUM(items_sold) as items')
            ->groupBy('user_id')
            ->orderByDesc('gmv')
            ->limit(5)
            ->get();

        $tugasSelesai = TaskReport::where('task_status', 'COMPLETED')->count();
        $tugasProses = TaskReport::where('task_status', 'PENDING')->count();

        $imports = ImportHistory::with('coreMetrics')->orderBy('start_date', 'desc')->limit(7)->get()->reverse();
        $trenLabels = []; $trenGmv = []; $trenItems = [];

        foreach($imports as $imp) {
            $trenLabels[] = Carbon::parse($imp->start_date)->format('d M');
            $trenGmv[] = $imp->coreMetrics->sum('affiliate_gmv');
            $trenItems[] = $imp->coreMetrics->sum('items_sold');
        }

        return [
            'metrics' => [
                'gmv' => ['val' => $currCore->gmv, 'trend' => $this->calcTrend($currCore->gmv, $prevCore->gmv)],
                'items' => ['val' => $currCore->items, 'trend' => $this->calcTrend($currCore->items, $prevCore->items)],
                'komisi' => ['val' => $currCore->commission, 'trend' => $this->calcTrend($currCore->commission, $prevCore->commission)],
                'sampel' => ['val' => $currSampel, 'trend' => $this->calcTrend($currSampel, $prevSampel)],
            ],
            'top5' => $top5,
            'statusTugas' => [
                ['label' => 'Selesai', 'value' => $tugasSelesai, 'color' => '#10b981'],
                ['label' => 'Diproses', 'value' => $tugasProses, 'color' => '#f59e0b'],
            ],
            'trenHarian' => [
                'labels' => empty($trenLabels) ? ['Belum ada data'] : $trenLabels,
                'gmv' => empty($trenGmv) ? [0] : $trenGmv,
                'items' => empty($trenItems) ? [0] : $trenItems,
            ]
        ];
    }

    private function getDetailRoiData(Request $request)
    {
        $videoGmv = VideoProductMetric::sum('video_gmv');
        $liveGmv = LiveProductMetric::sum('live_gmv');
        
        $products = ProductMetric::with('product')
            ->selectRaw('product_id, SUM(affiliate_gmv) as total_gmv, SUM(samples_sent) as total_samples_sent, SUM(attributed_orders) as total_orders')
            ->groupBy('product_id')
            ->get()
            ->map(function($item) {
                $cost = SampleRequest::whereHas('details', function ($query) use ($item) {
                    $query->where('product_id', $item->product_id);
                })->sum('shipping_cost') ?? 0;

                $sentQuantity = (SampleRequestDetail::where('product_id', $item->product_id)->sum('quantity') ?? 0) + ($item->total_samples_sent ?? 0);

                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'cat' => $item->product->category ?? 'Umum',
                    'sent' => $sentQuantity . ' unit',
                    'cost' => $cost,
                    'gmv' => $item->total_gmv,
                    'orders' => $item->total_orders ?? 0,
                    'roi' => $cost > 0 ? number_format($item->total_gmv / $cost, 1) . 'x' : '0x'
                ];
            });

        return [
            'sumberKonversi' => [
                ['label' => 'TikTok Video', 'value' => $videoGmv ?: 1, 'color' => '#3b82f6'],
                ['label' => 'TikTok Live', 'value' => $liveGmv ?: 1, 'color' => '#ec4899'],
            ],
            'products' => $products,
            'totalOrders' => $products->sum('orders')
        ];
    }

    private function getCoreMetrics($month, $year)
    {
        $query = CoreMetric::whereHas('importHistory', function($q) use($month, $year) {
            $q->whereMonth('start_date', $month)->whereYear('start_date', $year);
        });

        return (object) [
            'gmv' => (float) $query->clone()->sum('affiliate_gmv'),
            'items' => (int) $query->clone()->sum('items_sold'),
            'commission' => (float) $query->clone()->sum('estimated_commission'),
            'refunds' => (float) $query->clone()->sum('refunds'),
            'samples' => (int) $query->clone()->sum('samples_sent'),
        ];
    }

    private function calcTrend($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / abs($previous)) * 100;
    }
}