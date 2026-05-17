<?php

namespace App\Services\Affiliator;

use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SampleRequestService
{
    /**
     * Memfilter data berdasarkan tab aktif
     */
    public function getTabData($tab, Request $request)
    {
        switch ($tab) {
            case 'shipped':
                return $this->getShippedData($request);
            case 'request-sample':
            default:
                return $this->getAllData($request);
        }
    }
    public function getAllData(Request $request): LengthAwarePaginator
    {
        $user = auth()->user(); 

        $query = SampleRequest::where('user_id', $user->id)
            ->where('status', ['PENDING','APPROVED', 'REJECTED'])
            ->with(['details.product']) 
            ->latest();

        return $query->paginate(10)->withQueryString();
    }

    public function getShippedData(Request $request): LengthAwarePaginator
    {
        $user = auth()->user(); 
        $query = SampleRequest::where('user_id', $user->id)
            ->where('status', 'SHIPPED')
            ->with(['details.product'])
            ->latest();

        return $query->paginate(10)->withQueryString();
    }
    public function getRequestDetail(User $user, int $id): SampleRequest
    {
        return SampleRequest::where('user_id', $user->id)
            ->with(['sampleRequestDetails.product'])
            ->findOrFail($id);
    }
}