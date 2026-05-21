<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SampleRequest;
use App\Notifications\SampleStatusNotification;
use App\Services\Admin\RequestSampleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class RequestSampleController extends Controller
{
    protected $requestSampleService;

    public function __construct(RequestSampleService $requestSampleService)
    {
        $this->requestSampleService = $requestSampleService;
    }

    public function index()
    {
        return view('pages.admin.sample-request.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = SampleRequest::with(['user', 'details.product'])
                ->withSum('details','quantity');
                    return DataTables::of($query)
                    ->addIndexColumn()
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
                            'REJECTED' => 'Ditolak',
                            'DELIVERED' => 'Terkirim',
                        ];

                        $displayStatus = $statusIndo[$row->status] ?? $row->status;
                        return view('components.atoms.badge', [
                            'slot' => $displayStatus,
                            'status' => strtolower($row->status) 
                        ])->render();
                    })
                    ->editColumn('details_sum_quantity', function ($row) {
                        return $row->details_sum_quantity . ' Produk' ?? 0; 
                    })
                    ->editColumn('created_at', function($row) {
                        return Carbon::parse($row->created_at)->format('d M Y');
                    })
                    ->addColumn('action', function($row) {
                        return view('pages.admin.sample-request.action-buttons', compact('row'))->render();
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
    }


    public function approve(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id',
        ]);

        try {
            $this->requestSampleService->approve($request->sample_request_id);
            
            $sampleRequest = SampleRequest::with('user')->find($request->sample_request_id);
            if ($sampleRequest && $sampleRequest->user) {
                $sampleRequest->user->notify(new SampleStatusNotification($sampleRequest, 'APPROVED'));
            }

            return redirect()->back()->with('success', 'Pengajuan keseluruhan berhasil disetujui');
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
        $this->requestSampleService->ship(
            $request->sample_request_id, 
            $request->only(['courier', 'tracking_number', 'shipping_cost'])
        );
        $sampleRequest = SampleRequest::with('user')->find($request->sample_request_id);
        if ($sampleRequest && $sampleRequest->user) {
            $sampleRequest->user->notify(new SampleStatusNotification($sampleRequest, 'SHIPPED'));
        }

        return redirect()->back()->with('success', 'Produk berhasil dikirim.');
    }

    public function approveProduct(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:sample_request_details,id',
            'mandatory_video_count' => 'required|integer|min:1',
        ]);

        $this->requestSampleService->approveProduct(
            $request->detail_id,
            $request->mandatory_video_count
        );

        return redirect()->back()->with('success', 'Produk berhasil disetujui dengan penugasan video.');
    }

    public function rejectProduct(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:sample_request_details,id',
            'reject_reason' => 'required|string|max:500',
        ]);

        $this->requestSampleService->rejectProduct(
            $request->detail_id,
            $request->reject_reason
        );

        return redirect()->back()->with('success', 'Pengajuan produk berhasil ditolak.');
    }

    public function syncStatus()
    {
        $sampleRequests = SampleRequest::whereIn('status', ['SHIPPED'])
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier')
            ->get();

        foreach ($sampleRequests as $item) {
            
            $model = $item instanceof SampleRequest 
                ? $item 
                : SampleRequest::find($item->id);
            
            if ($model) {
                $this->requestSampleService->checkAndUpdateDeliveryStatus($model);
            }
            
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

        $this->requestSampleService->rejectRequest(
            $request->sample_request_id,
            $request->reject_reason
        );

        return redirect()->back()->with('success', 'Pengajuan sampel telah ditolak.');
    }
}
