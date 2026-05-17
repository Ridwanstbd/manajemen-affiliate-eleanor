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
        $tab = $request->query('tab','request-sample');
                                     
        $data = $this->sampleRequestService->getTabData($tab, $request);
        $viewData = ['currentTab' => $tab];

        if (is_array($data)) {
            $viewData = [...$viewData, ...$data];
        } else {
            $viewData['data'] = $data;
        }

        return view('pages.affiliator.sample-request.index', $viewData);
    }
        
    public function showAll(Request $request)
    {
        $filters = $request->only(['status']);
        
    }

    public function show($id)
    {
        try {
            $sampleRequest = $this->sampleRequestService->getRequestDetail(auth()->user(), (int)$id);
            
            return view('pages.affiliator.sample-request.show', compact('sampleRequest'));
        } catch (\Exception $e) {
            return redirect()->route('affiliator.sample-request.index')
                ->with('error', 'Data pengajuan sampel tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }
}