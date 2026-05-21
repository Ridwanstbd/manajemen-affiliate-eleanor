<?php

namespace App\Services\Affiliator;

use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SampleRequestService
{
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
            ->whereIn('status', ['PENDING','APPROVED', 'REJECTED'])
            ->with(['details.product']) 
            ->latest();

        return $query->paginate(10)->withQueryString();
    }

    public function getShippedData(Request $request): LengthAwarePaginator
    {
        $user = auth()->user(); 
        
        $query = SampleRequest::where('user_id', $user->id)
            ->whereIn('status', ['SHIPPED', 'DELIVERED'])
            ->with(['details.product'])
            ->latest();

        return $query->paginate(10)->withQueryString();
    }

    public function getRequestDetail(User $user, int $id): array
    {
        $sampleRequest = SampleRequest::where('user_id', $user->id)
            ->with(['details.product'])
            ->findOrFail($id);

        $timeline = [];

        if ($sampleRequest->status === 'PENDING') {
            $timeline[] = [
                'title' => 'Pengajuan Diterima',
                'description' => 'Sistem telah menerima pengajuan sampel Anda dan sedang menunggu peninjauan oleh tim admin.',
                'time' => $sampleRequest->created_at->translatedFormat('d M Y, H:i'),
                'is_completed' => true,
                'icon' => 'check-circle'
            ];
        } elseif ($sampleRequest->status === 'APPROVED') {
            $timeline[] = [
                'title' => 'Pengajuan Disetujui',
                'description' => 'Pengajuan sampel Anda telah disetujui. Tim kami sedang menyiapkan paket untuk dikirim.',
                'time' => $sampleRequest->updated_at->translatedFormat('d M Y, H:i'),
                'is_completed' => true,
                'icon' => 'check-circle'
            ];
        } elseif ($sampleRequest->status === 'REJECTED') {
            $timeline[] = [
                'title' => 'Pengajuan Ditolak',
                'description' => 'Maaf, pengajuan sampel Anda ditolak. Alasan: ' . ($sampleRequest->reject_reason ?? 'Tidak ada keterangan.'),
                'time' => $sampleRequest->updated_at->translatedFormat('d M Y, H:i'),
                'is_completed' => true,
                'icon' => 'close-button'
            ];
        }

        if (in_array($sampleRequest->status, ['SHIPPED', 'DELIVERED'])) {
            if ($sampleRequest->tracking_number && $sampleRequest->courier) {
                try {
                    $courierCode = strtolower($sampleRequest->courier);
                    $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?awb={$sampleRequest->tracking_number}&courier={$courierCode}";
                    $response = Http::withHeaders([
                        'key' => env('RAJAONGKIR_API_KEY')
                    ])->post($url);
                    
                    $responseData = $response->json();
                    
                    if (isset($responseData['data']['manifest'])) {
                        $isDelivered = false;
                        foreach ($responseData['data']['manifest'] as $manifest) {
                            $manifestDate = isset($manifest['manifest_date']) ? $manifest['manifest_date'] : '';
                            $manifestTime = isset($manifest['manifest_time']) ? $manifest['manifest_time'] : '';
                            $dateTime = trim("$manifestDate $manifestTime");

                            $timeline[] = [
                                'title' => $manifest['manifest_description'] ?? 'Update Status',
                                'description' => $manifest['city_name'] ?? '',
                                'time' => $dateTime,
                                'is_completed' => true,
                                'icon' => 'truck'
                            ];

                            if (isset($manifest['manifest_description']) && strtolower($manifest['manifest_description']) === 'delivered') {
                                $isDelivered = true;
                            }
                        }

                        if ($isDelivered && $sampleRequest->status !== 'DELIVERED') {
                            $sampleRequest->update(['status' => 'DELIVERED']);
                        }
                    }
                } catch (\Exception $e) {
                    $timeline[] = [
                        'title' => 'Gagal Memuat Pelacakan Kurir',
                        'description' => 'Sistem gagal menghubungkan layanan kurir logistik saat ini. No. Resi: ' . $sampleRequest->tracking_number,
                        'time' => null,
                        'is_completed' => false,
                        'icon' => 'truck'
                    ];
                }
            } else {
                $isShipped = in_array($sampleRequest->status, ['SHIPPED', 'DELIVERED']);
                $timeline[] = [
                    'title' => 'Paket Dalam Pengiriman',
                    'description' => $isShipped 
                        ? 'Paket telah diserahkan ke kurir dengan nomor resi ' . $sampleRequest->tracking_number . '.'
                        : 'Nomor resi pengiriman akan muncul di sini setelah paket diserahkan ke pihak kurir.',
                    'time' => $isShipped ? $sampleRequest->updated_at->translatedFormat('d M Y, H:i') : null,
                    'is_completed' => $isShipped,
                    'icon' => 'truck'
                ];
            }
        }

        return [
            'sampleRequest' => $sampleRequest,
            'timeline' => $timeline
        ];
    }
}