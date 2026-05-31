<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SampleRequest;
use App\Services\Admin\RequestSampleService;
use Illuminate\Support\Facades\DB;

class SyncDeliveryStatus extends Command
{
    protected $signature = 'app:sync-delivery-status';
    protected $description = 'Cek otomatis resi pesanan APPROVED dan ubah ke SHIPPED jika terkirim';

    public function handle(RequestSampleService $service)
    {
        $shippedRequests = SampleRequest::where('status', 'SHIPPED')
            ->whereNotNull('tracking_number')
            ->whereNotNull('courier')
            ->get();

        $updatedCount = 0;

        foreach ($shippedRequests as $item) {
            $service->checkAndUpdateDeliveryStatus($item);
            $updatedCount++;
        }

        $deliveredWithoutTasks = SampleRequest::where('status', 'DELIVERED')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sample_task_reports')
                    ->whereColumn('sample_task_reports.sample_request_id', 'sample_requests.id');
            })
            ->get();

        foreach ($deliveredWithoutTasks as $item) {
            $service->generateTaskForDelivered($item);
        }

        $this->info("Pengecekan selesai. {$updatedCount} pesanan diproses. " . $deliveredWithoutTasks->count() . " task tertunda dibuat.");
    }
}