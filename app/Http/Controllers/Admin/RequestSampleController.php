<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SampleRequest;
use App\Services\Admin\RequestSampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class RequestSampleController extends Controller
{
    protected $requestSampleService;

    public function __construct(RequestSampleService $requestSampleService)
    {
        $this->requestSampleService = $requestSampleService;
    }

    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'pending');
        
        return view('pages.admin.sample-request.index', compact('currentTab'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $tab = $request->query('tab', 'pending');
            $statusMap = [
                'pending' => 'PENDING',
                'disetujui' => 'APPROVED',
                'dalam-perjalanan' => 'SHIPPED',
                'ditolak' => 'REJECTED',
                'terkirim' => 'DELIVERED',
            ];
            
            $status = $statusMap[$tab] ?? 'PENDING';

            $query = SampleRequest::with(['user', 'details.product'])
                ->where('status', $status)
                ->withSum('details', 'quantity');
                
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('d M Y, H:i');
                })
                ->addColumn('username', function ($row) {
                    if ($row->user && $row->user->username) {
                        return '@' . $row->user->username; 
                    }
                    return 'Tidak Diketahui'; 
                })
                ->addColumn('status', function($row) {
                    $statusIndo = [
                        'PENDING'  => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'SHIPPED'  => 'Dalam Perjalanan',
                        'DELIVERED'=> 'Terkirim',
                        'REJECTED' => 'Ditolak',
                        'COMPLETED'=> 'Selesai'
                    ];
                    
                    $statusBadge = [
                        'PENDING'  => 'pending',
                        'APPROVED' => 'paid',
                        'SHIPPED'  => 'shipped',
                        'DELIVERED'=> 'shipped',
                        'REJECTED' => 'cancelled',
                        'COMPLETED'=> 'paid'
                    ];

                    $label = $statusIndo[$row->status] ?? $row->status;
                    $class = $statusBadge[$row->status] ?? 'pending';

                    return view('components.atoms.badge', [
                        'slot' => $label,
                        'status' => $class
                    ])->render();
                })
                ->addColumn('action', function($row) {
                    return view('pages.admin.sample-request.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function approve(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id'
        ]);

        try {
            $this->requestSampleService->approve($request->sample_request_id);
            return redirect()->back()->with('success', 'Pengajuan sampel berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function ship(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id',
            'courier' => 'required|string',
            'tracking_number' => 'required|string',
            'shipping_cost' => 'nullable|numeric'
        ]);

        try {
            $this->requestSampleService->ship($request->sample_request_id, $request->all());
            return redirect()->back()->with('success', 'Status logistik berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approveProduct(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:sample_request_details,id',
            'mandatory_video_count' => 'required|integer|min:1'
        ]);

        try {
            $this->requestSampleService->approveProduct($request->detail_id, $request->mandatory_video_count);
            return redirect()->back()->with('success', 'Produk berhasil disetujui untuk pengajuan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectProduct(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:sample_request_details,id',
            'reject_reason' => 'required|string|max:500'
        ]);

        try {
            $this->requestSampleService->rejectProduct($request->detail_id, $request->reject_reason);
            return redirect()->back()->with('success', 'Produk berhasil ditolak dari pengajuan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function syncStatus(Request $request)
    {
        $shippedRequests = SampleRequest::where('status', 'SHIPPED')->get();
        foreach ($shippedRequests as $item) {
            $this->requestSampleService->checkAndUpdateDeliveryStatus($item);
        }

        $deliveredWithoutTasks = SampleRequest::where('status', 'DELIVERED')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sample_task_reports')
                    ->whereColumn('sample_task_reports.sample_request_id', 'sample_requests.id');
            })
            ->get();

        foreach ($deliveredWithoutTasks as $item) {
            $this->requestSampleService->generateTaskForDelivered($item);
        }

        return redirect()->back()->with('success', 'Sinkronisasi pelacakan selesai. Status pengiriman telah diperbarui.');
    }

    public function track($id)
    {
        $sampleRequest = SampleRequest::findOrFail($id);

        $responseData = $this->requestSampleService->getTrackingTimeline($sampleRequest);

        if (!$responseData) {
            return response()->json(['error' => 'Gagal menghubungi server pelacakan atau data resi tidak lengkap.'], 500);
        }

        $finalResponse = $responseData['rajaongkir'] ?? [];
        $finalResponse['internal_timeline'] = $responseData['timeline'];

        return response()->json($finalResponse);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id',
            'reject_reason' => 'required|string|max:500' 
        ]);

        try {
            $this->requestSampleService->rejectRequest(
                $request->sample_request_id,
                $request->reject_reason
            );

            return redirect()->back()->with('success', 'Pengajuan sampel telah ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}