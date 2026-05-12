<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SampleRequest;
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
                    ->addColumn('username', function ($row) {
                        if ($row->user && $row->user->username) {
                            return '@' . $row->user->username; 
                        }
                        return 'Tidak Diketahui'; 
                    })
                    ->addColumn('status', function($row) {
                        return view('components.atoms.badge', [
                            'slot' => $row->status,
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


    public function updateResi(Request $request)
    {
        $request->validate([
            'sample_request_id' => 'required|exists:sample_requests,id',
            'courier' => 'required|string',
            'tracking_number' => 'required|string',
            'shipping_cost' => 'nullable|numeric'
        ]);

        $this->requestSampleService->updateResi(
            $request->sample_request_id, 
            $request->only(['courier', 'tracking_number', 'shipping_cost'])
        );

        return redirect()->back()->with('success', 'Produk berhasil dikirim dan status diubah menjadi APPROVED.');
    }

    public function syncStatus()
    {
        $sampleRequests = SampleRequest::where('status', 'APPROVED')
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

        $responseData = $this->requestSampleService->checkAndUpdateDeliveryStatus($sampleRequest);

        if (!$responseData) {
            return response()->json(['error' => 'Gagal menghubungi server pelacakan atau data resi tidak lengkap.'], 500);
        }

        return response()->json($responseData);
    }
}
