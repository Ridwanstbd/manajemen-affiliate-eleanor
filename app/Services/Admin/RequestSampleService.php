<?php

namespace App\Services\Admin;

use App\Models\SampleRequest;
use App\Models\SampleRequestDetail;
use App\Models\Setting;
use App\Models\TaskReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RequestSampleService
{
    public function approve(int $id)
    {
        $sampleRequest = SampleRequest::with('details')->findOrFail($id);
        
        $hasPending = $sampleRequest->details->contains('status', 'PENDING');
        if ($hasPending) {
            throw new \Exception('Gagal menyetujui. Pastikan semua produk dalam pengajuan ini telah disetujui atau ditolak terlebih dahulu.');
        }

        $hasApproved = $sampleRequest->details->contains('status', 'APPROVED');
        if (!$hasApproved) {
            throw new \Exception('Semua produk ditolak. Silakan gunakan tombol tolak pengajuan (Reject Request) secara keseluruhan.');
        }

        $sampleRequest->update([
            'status' => 'APPROVED', 
        ]);

        return $sampleRequest;
    }

    public function ship(int $id, array $data)
    {
        $sampleRequest = SampleRequest::findOrFail($id);
        $sampleRequest->update([
            'status' => 'SHIPPED', 
            'courier' => $data['courier'],
            'tracking_number' => $data['tracking_number'],
            'shipping_cost' => $data['shipping_cost'] ?? 0,
        ]);

        return $sampleRequest;
    }

    public function approveProduct(int $detailId, int $mandatoryVideoCount)
    {
        $detail = SampleRequestDetail::findOrFail($detailId);
        $detail->update([
            'status' => 'APPROVED',
            'mandatory_video_count' => $mandatoryVideoCount
        ]);

        return $detail;
    }

    public function rejectProduct(int $detailId, string $rejectReason)
    {
        $detail = SampleRequestDetail::findOrFail($detailId);
        $detail->update([
            'status' => 'REJECTED',
            'reject_reason' => $rejectReason
        ]);

        return $detail;
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
                if ($sampleRequest->status !== 'DELIVERED') {
                    $sampleRequest->update(['status' => 'DELIVERED']);
                }
                
                $this->generateTaskForSample($sampleRequest);
            }

            return $responseData;

        } catch (\Exception $e) {
            return null;
        }
    }
    public function getTrackingTimeline(SampleRequest $sampleRequest)
    {
        $rajaOngkirData = $this->checkAndUpdateDeliveryStatus($sampleRequest);

        $timeline = [];

        $timeline[] = [
            'title' => 'Pengajuan Sampel Dikirim',
            'description' => 'Permintaan sampel produk telah dikirim oleh affiliator dan masuk antrean sistem.',
            'time' => $sampleRequest->created_at->translatedFormat('d M Y, H:i'),
            'is_completed' => true,
            'icon' => 'paper-plane'
        ];

        if ($sampleRequest->status === 'REJECTED') {
            $timeline[] = [
                'title' => 'Pengajuan Ditolak',
                'description' => 'Alasan: ' . ($sampleRequest->reject_reason ?? 'Tidak ada alasan spesifik.'),
                'time' => $sampleRequest->updated_at->translatedFormat('d M Y, H:i'),
                'is_completed' => true,
                'is_danger' => true,
                'icon' => 'x-circle'
            ];
        } else {
            $isApprovedPast = in_array($sampleRequest->status, ['APPROVED', 'SHIPPED']);
            $timeline[] = [
                'title' => 'Pengajuan Disetujui',
                'description' => $isApprovedPast 
                    ? 'Admin telah menyetujui permintaan. Paket dipersiapkan untuk diserahkan ke ekspedisi.' 
                    : 'Menunggu peninjauan dan persetujuan dari tim administrator.',
                'time' => $sampleRequest->status !== 'PENDING' ? $sampleRequest->updated_at->translatedFormat('d M Y, H:i') : null,
                'is_completed' => $isApprovedPast,
                'icon' => 'check-circle'
            ];

            $isShipped = $sampleRequest->status === 'SHIPPED';
            $timeline[] = [
                'title' => 'Paket Dalam Pengiriman',
                'description' => $isShipped 
                    ? 'Paket telah diserahkan ke kurir ' . strtoupper($sampleRequest->courier ?? 'mitra') . ' dengan nomor resi ' . $sampleRequest->tracking_number . '.'
                    : 'Nomor resi pengiriman akan muncul setelah paket diserahkan ke pihak kurir.',
                'time' => $isShipped ? $sampleRequest->updated_at->translatedFormat('d M Y, H:i') : null,
                'is_completed' => $isShipped,
                'icon' => 'truck'
            ];
        }

        return [
            'rajaongkir' => $rajaOngkirData,
            'timeline'   => $timeline
        ];
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
            
            if ($product && $detail->status === 'APPROVED') {
                $mandatoryCount = $detail->mandatory_video_count > 0 ? $detail->mandatory_video_count : 1;

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