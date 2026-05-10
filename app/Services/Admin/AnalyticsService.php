<?php

namespace App\Services\Admin;

use App\Models\CreatorMetric;
use App\Models\KOLContract;
use App\Models\Product;
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
        $isKol = $request->boolean('is_kol', false);

        $currCore = $this->getCoreMetrics($currentMonth->month, $currentMonth->year, $isKol);
        $prevCore = $this->getCoreMetrics($prevMonth->month, $prevMonth->year, $isKol);

        $currBiayaSampel = $this->getTotalCost($currentMonth->month, $currentMonth->year, $isKol);
        $prevBiayaSampel = $this->getTotalCost($prevMonth->month, $prevMonth->year, $isKol);

        $currRoi = $currBiayaSampel > 0 ? ($currCore->gmv / $currBiayaSampel) : 0;
        $prevRoi = $prevBiayaSampel > 0 ? ($prevCore->gmv / $prevBiayaSampel) : 0;

        $totalPermintaan = SampleRequest::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->whereHas('user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })
            ->count();
        
        $disetujui = SampleRequest::whereIn('status', ['APPROVED', 'SHIPPED'])
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->whereHas('user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })
            ->count() + ($currCore->samples ?? 0);
        
        $kontenDibuat = TaskReport::where('task_status', 'COMPLETED')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->whereHas('sampleRequests.user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })
            ->count();
            
        $konversi = VideoProductMetric::whereHas('video.user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })->sum('orders') + 
                    LiveProductMetric::whereHas('liveStream.user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })->sum('orders');

        $funnel = [
            'total' => $totalPermintaan,
            'approved' => $disetujui,
            'approved_pct' => $totalPermintaan > 0 ? round(($disetujui / $totalPermintaan) * 100) : 0,
            'content' => $kontenDibuat,
            'content_pct' => $disetujui > 0 ? round(($kontenDibuat / $disetujui) * 100) : 0,
            'conversion' => $konversi,
        ];

        $topProducts = $this->getProductPerformance($currentMonth->month, $currentMonth->year, $isKol)
            ->sortByDesc('gmv')
            ->take(3)
            ->values();

        $m1 = $request->input('month_1', $prevMonth->format('Y-m'));
        $m2 = $request->input('month_2', $currentMonth->format('Y-m'));
        $date1 = Carbon::parse($m1 . '-01');
        $date2 = Carbon::parse($m2 . '-01');

        $core1 = $this->getCoreMetrics($date1->month, $date1->year, $isKol);
        $core2 = $this->getCoreMetrics($date2->month, $date2->year, $isKol);

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
        $isKol = $request->boolean('is_kol', false);

        $currCore = $this->getCoreMetrics($currentMonth->month, $currentMonth->year, $isKol);
        $prevCore = $this->getCoreMetrics($prevMonth->month, $prevMonth->year, $isKol);
        
        $currSampel = SampleRequest::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->whereHas('user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })
            ->count() + $currCore->samples;
                                   
        $prevSampel = SampleRequest::whereMonth('created_at', $prevMonth->month)
            ->whereYear('created_at', $prevMonth->year)
            ->whereHas('user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })
            ->count() + $prevCore->samples;

        $top5 = CreatorMetric::with('user')
            ->whereHas('user', function($q) use ($isKol) {
                $q->where('is_kol', $isKol);
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
            'trenHarian' => ['labels' => ['Belum ada data'], 'gmv' => [0], 'items' => [0]] 
        ];
    }

    private function getDetailRoiData(Request $request)
    {
        $currentMonth = $this->getTargetDate($request);
        $isKol = $request->boolean('is_kol', false);

        $videoGmv = VideoProductMetric::whereHas('video.user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })->sum('video_gmv');
        $liveGmv = LiveProductMetric::whereHas('liveStream.user', function($q) use ($isKol) { $q->where('is_kol', $isKol); })->sum('live_gmv');
        
        $products = $this->getProductPerformance($currentMonth->month, $currentMonth->year, $isKol);

        return [
            'sumberKonversi' => [
                ['label' => 'TikTok Video', 'value' => $videoGmv ?: 1, 'color' => '#3b82f6'],
                ['label' => 'TikTok Live', 'value' => $liveGmv ?: 1, 'color' => '#ec4899'],
            ],
            'products' => $products,
            'totalOrders' => collect($products)->sum('orders')
        ];
    }

    private function getCoreMetrics($month, $year, $isKol)
    {
        $query = CreatorMetric::whereHas('importHistory', function($q) use($month, $year) {
            $q->whereMonth('start_date', $month)->whereYear('start_date', $year);
        })->whereHas('user', function($q) use ($isKol) {
            $q->where('is_kol', $isKol);
        });

        return (object) [
            'gmv' => (float) $query->clone()->sum('affiliate_gmv'),
            'items' => (int) $query->clone()->sum('items_sold'),
            'commission' => (float) $query->clone()->sum('estimated_commission'),
            'refunds' => (float) $query->clone()->sum('refunds'),
            'samples' => (int) $query->clone()->sum('samples_sent'),
        ];
    }

    private function getTotalCost($month, $year, $isKol)
    {
        $shippingCost = SampleRequest::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereHas('user', function($q) use ($isKol) {
                $q->where('is_kol', $isKol);
            })->sum('shipping_cost') ?? 0;

        $contractFee = 0;
        if ($isKol) {
            $contractFee = KOLContract::whereMonth('start_date', $month)
                ->whereYear('start_date', $year)
                ->sum('contract_fee') ?? 0;
        }

        return $shippingCost + $contractFee;
    }

    private function getProductPerformance($month, $year, $isKol)
    {
        $videoMetrics = VideoProductMetric::whereHas('video.user', function($q) use ($isKol) {
            $q->where('is_kol', $isKol);
        })->get();

        $liveMetrics = LiveProductMetric::whereHas('liveStream.user', function($q) use ($isKol) {
            $q->where('is_kol', $isKol);
        })->get();

        $productIds = $videoMetrics->pluck('product_id')->merge($liveMetrics->pluck('product_id'))->unique();
        
        return Product::whereIn('id', $productIds)->get()->map(function($product) use ($videoMetrics, $liveMetrics, $isKol) {
            $pVideo = $videoMetrics->where('product_id', $product->id);
            $pLive = $liveMetrics->where('product_id', $product->id);

            $gmv = $pVideo->sum('video_gmv') + $pLive->sum('live_gmv');
            $orders = $pVideo->sum('orders') + $pLive->sum('orders');

            $costSampel = SampleRequest::whereHas('details', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })->whereHas('user', function($q) use ($isKol) {
                $q->where('is_kol', $isKol);
            })->sum('shipping_cost') ?? 0;

            $costContract = 0;
            if ($isKol) {
                $costContract = KOLContract::whereHas('products', function($q) use ($product) {
                    $q->where('products.id', $product->id);
                })->sum('contract_fee') ?? 0;
            }

            $totalCost = $costSampel + $costContract;

            $sentQuantity = SampleRequestDetail::where('product_id', $product->id)
                ->whereHas('sampleRequest.user', function($q) use ($isKol) {
                    $q->where('is_kol', $isKol);
                })->sum('quantity') ?? 0;

            return [
                'name' => $product->name ?? 'Unknown',
                'cat' => $product->category ?? 'Umum',
                'sent' => $sentQuantity . ' unit',
                'cost' => $totalCost,
                'gmv' => $gmv,
                'orders' => $orders,
                'roi' => $totalCost > 0 ? number_format($gmv / $totalCost, 1) . 'x' : '0x'
            ];
        });
    }

    private function calcTrend($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / abs($previous)) * 100;
    }
}