<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AnalyticsService;
use App\Models\ImportHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'analytics');
        $isKol = $request->boolean('is_kol', false);
        
        $availableMonths = ImportHistory::selectRaw('DISTINCT DATE_FORMAT(start_date, "%Y-%m") as month_val')
            ->orderBy('month_val', 'desc')
            ->get()
            ->map(function($item) {
                $date = Carbon::parse($item->month_val . '-01');
                return [
                    'value' => $item->month_val,
                    'label' => $date->translatedFormat('F Y') 
                ];
            });

        $data = $this->analyticsService->getTabData($tab, $request);
        
        $selectedMonth = $request->input('selected_month');
        $selectedLabel = 'Pilih Bulan';
        if ($selectedMonth) {
            $selectedLabel = Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');
        } elseif ($availableMonths->isNotEmpty()) {
            $selectedLabel = $availableMonths->first()['label'];
        }

        $viewData = array_merge([
            'currentTab' => $tab,
            'availableMonths' => $availableMonths,
            'selectedMonthLabel' => $selectedLabel,
            'isKol' => $isKol
        ], $data);

        return view('pages.admin.data-center.index', $viewData);
    }

    public function detailRoiData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->analyticsService->getTabData('detail', $request);
            
            $products = collect($data['products']); 

            return DataTables::of($products)
                ->addColumn('name_cat', function($row) {
                    return '<div style="font-weight: 700; color: var(--text-primary);">' . $row['name'] . '</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">' . $row['cat'] . '</div>';
                })
                ->addColumn('cost_formatted', function($row) {
                    return 'Rp ' . number_format($row['cost'], 0, ',', '.');
                })
                ->addColumn('gmv_formatted', function($row) {
                    return 'Rp ' . number_format($row['gmv'], 0, ',', '.');
                })
                ->addColumn('roi_badge', function($row) {
                    return '<span style="display:inline-block; padding:4px 8px; border-radius:4px; font-weight:600; font-size:14px; background:rgba(16, 185, 129, 0.1); color:#10b981;">' . $row['roi'] . '</span>';
                })
                ->rawColumns(['name_cat', 'roi_badge']) 
                ->make(true);
        }
    }
}