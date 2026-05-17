<?php

namespace App\Services\Affiliator;

use App\Models\SampleRequest;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
    public function getRequestDetail(User $user, int $id): array
    {
        $sampleRequest = SampleRequest::where('user_id', $user->id)
            ->with(['details.product'])
            ->findOrFail($id);

        $timeline = [];

        $timeline[] = [
            'title' => 'Pengajuan Sampel Dikirim',
            'description' => 'Permintaan sampel produk Anda telah berhasil dikirim dan masuk antrean sistem.',
            'time' => $sampleRequest->created_at->translatedFormat('d M Y, H:i'),
            'is_completed' => true,
            'icon' => 'paper-plane'
        ];

        if ($sampleRequest->status === 'REJECTED') {
            $timeline[] = [
                'title' => 'Pengajuan Ditolak oleh Admin',
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
                    ? 'Admin telah menyetujui permintaan Anda. Paket sedang dipersiapkan untuk diserahkan ke ekspedisi.' 
                    : 'Menunggu peninjauan dan persetujuan dari tim administrator.',
                'time' => $sampleRequest->status !== 'PENDING' ? $sampleRequest->updated_at->translatedFormat('d M Y, H:i') : null,
                'is_completed' => $isApprovedPast,
                'icon' => 'check-circle'
            ];

            if ($isApprovedPast && $sampleRequest->tracking_number && $sampleRequest->courier) {
                try {
                    $courierCode = strtolower($sampleRequest->courier);
                    $url = "https://rajaongkir.komerce.id/api/v1/track/waybill?awb={$sampleRequest->tracking_number}&courier={$courierCode}";

                    $response = Http::withHeaders([
                        'key' => env('RAJAONGKIR_API_KEY')
                    ])->post($url);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        
                        if (isset($responseData['data']['manifest']) && is_array($responseData['data']['manifest'])) {
                            foreach ($responseData['data']['manifest'] as $manifest) {
                                $manifestDate = $manifest['manifest_date'] ?? '';
                                $manifestTime = $manifest['manifest_time'] ?? '';
                                $formattedTime = trim($manifestDate . ' ' . $manifestTime);

                                $timeline[] = [
                                    'title' => $manifest['manifest_description'] ?? 'Paket dalam perjalanan',
                                    'description' => 'Posisi: ' . ($manifest['city_name'] ?? 'Transit Gudang Logistik'),
                                    'time' => !empty($formattedTime) ? \Carbon\Carbon::parse($formattedTime)->translatedFormat('d M Y, H:i') : null,
                                    'is_completed' => true,
                                    'icon' => 'truck'
                                ];
                            }
                        }
                        $isDelivered = false;
                        if (isset($responseData['data']['delivered']) && $responseData['data']['delivered'] === true) {
                            $isDelivered = true;
                        } elseif (isset($responseData['data']['summary']['status']) && strtoupper($responseData['data']['summary']['status']) === 'DELIVERED') {
                            $isDelivered = true;
                        }

                        if ($isDelivered && $sampleRequest->status !== 'SHIPPED') {
                            $sampleRequest->update(['status' => 'SHIPPED']);
                        }
                    }
                } catch (\Exception $e) {$timeline[] = [
                        'title' => 'Gagal Memuat Pelacakan Kurir',
                        'description' => 'Sistem gagal menghubungkan layanan kurir logistik saat ini. No. Resi: ' . $sampleRequest->tracking_number,
                        'time' => null,
                        'is_completed' => false,
                        'icon' => 'truck'
                    ];
                }
            } else {
                $isShipped = $sampleRequest->status === 'SHIPPED';
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