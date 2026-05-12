<?php

namespace App\Services\Admin;

use App\Models\SampleRequest;
use App\Models\Setting;
use App\Models\TaskReport;
use Illuminate\Support\Facades\DB;
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

            if ($isDelivered) {
                if ($sampleRequest->status !== 'SHIPPED') {
                    $sampleRequest->update(['status' => 'SHIPPED']);
                }
                
                $this->generateTaskForSample($sampleRequest);
            }

            return $responseData;

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function generateTaskForSample(SampleRequest $sampleRequest)
    {
        $hasTasks = DB::table('sample_task_reports')
            ->where('sample_request_id', $sampleRequest->id)
            ->exists();
            
        if ($hasTasks) {
            return;
        }

        $setting = Setting::where('key', 'task_deadline_days')->first();
        $deadlineDays = $setting ? (int) $setting->value : 7;
        $dueDate = now()->addDays($deadlineDays);

        $sampleRequest->load('details.product');

        foreach ($sampleRequest->details as $detail) {
            $product = $detail->product;
            
            if ($product) {
                $mandatoryCount = $product->mandatory_video_count > 0 ? $product->mandatory_video_count : 1;

                for ($i = 0; $i < $mandatoryCount; $i++) {
                    $taskReport = TaskReport::create([
                        'user_id' => $sampleRequest->user_id,
                        'task_status' => 'PROCESSING',
                        'due_date' => $dueDate,
                    ]);

                    $taskReport->sampleRequests()->attach($sampleRequest->id);

                    $taskReport->products()->attach($product->id);
                }
            }
        }
    }
    public function rejectRequest(int $id, string $reason)
    {
        $sampleRequest = SampleRequest::findOrFail($id);

        $sampleRequest->update([
            'status' => 'REJECTED',
            'reject_reason' => $reason
        ]);

        return $sampleRequest;
    }
}