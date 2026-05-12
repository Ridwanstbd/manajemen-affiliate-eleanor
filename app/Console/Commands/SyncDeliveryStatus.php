<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SampleRequest;
use App\Services\Admin\RequestSampleService;

class SyncDeliveryStatus extends Command
{
    protected $signature = 'app:sync-delivery-status';
    protected $description = 'Cek otomatis resi pesanan APPROVED dan ubah ke SHIPPED jika terkirim';

    public function handle(RequestSampleService $service)
    {
        $sampleRequests  = SampleRequest::where('status', 'APPROVED')
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier')
            ->get();

        $updatedCount = 0;

        foreach ($sampleRequests as $item) {
            
            $model = $item instanceof SampleRequest 
                ? $item 
                : SampleRequest::find($item->id);
            
            if ($model) {
                $service->checkAndUpdateDeliveryStatus($model);
            }
            
        }

        $this->info("Pengecekan selesai. {$updatedCount} pesanan otomatis diubah menjadi SHIPPED.");
    }
}