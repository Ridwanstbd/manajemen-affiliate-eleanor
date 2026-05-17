<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Services\Affiliator\SampleRequestService;
use Illuminate\Http\Request;

class SampleRequestController extends Controller
{
    public function __construct(
        protected SampleRequestService $sampleRequestService
    ) {}

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'request-sample');
                                     
        $data = $this->sampleRequestService->getTabData($tab, $request);
        
        if ($request->ajax() || $request->wantsJson()) {
            $viewPath = $tab === 'shipped' 
                ? 'pages.affiliator.sample-request.shipped.partials.items' 
                : 'pages.affiliator.sample-request.all.partials.items';

            $html = view($viewPath, compact('data'))->render();
            
            return response()->json([
                'html' => $html,
                'next_page_url' => $data->nextPageUrl()
            ]);
        }

        $viewData = [
            'currentTab' => $tab,
            'data' => $data
        ];

        return view('pages.affiliator.sample-request.index', $viewData);
    }
        
    public function show($id)
    {
        try {
            $detailData = $this->sampleRequestService->getRequestDetail(auth()->user(), (int)$id);
            return view('pages.affiliator.sample-request.detail', $detailData);
        } catch (\Exception $e) {
            return redirect()->route('affiliator.sample-request.index')
                ->with('error', 'Data pengajuan sampel tidak ditemukan atau Anda tidak memiliki hak akses.');
        }
    }
}