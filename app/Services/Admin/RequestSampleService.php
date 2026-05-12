<?php

namespace App\Services\Admin;

use App\Models\SampleRequest;
use Illuminate\Support\Facades\Http;

class RequestSampleService
{
    public function updateResi(int $id, array $data)
    {
        $sampleRequest = SampleRequest::findOrFail($id);
        
        $sampleRequest->update([
            'status' => 'APPROVED', 
            'courier' => $data['courier'],
            'tracking_number' => $data['tracking_number'],
            'shipping_cost' => $data['shipping_cost'] ?? 0,
        ]);

        return $sampleRequest;
    }

    public function checkAndUpdateDeliveryStatus(SampleRequest $sampleRequest)
    {
        if (!$sampleRequest->tracking_number || !$sampleRequest->courier) {
            return null;
        }

        try {
            $courierCode = strtolower($sampleRequest->courier);
            $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?awb={$sampleRequest->tracking_number}&courier={$courierCode}";

            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->post($url);

            $responseData = $response->json();

            $isDelivered = false;
            if (isset($responseData['data']['delivered']) && $responseData['data']['delivered'] === true) {
                $isDelivered = true;
            } elseif (isset($responseData['data']['summary']['status']) && strtoupper($responseData['data']['summary']['status']) === 'DELIVERED') {
                $isDelivered = true;
            }

            if ($isDelivered && $sampleRequest->status !== 'SHIPPED') {
                $sampleRequest->update(['status' => 'SHIPPED']);
            }

            return $responseData;

        } catch (\Exception $e) {
            return null;
        }
    }
}