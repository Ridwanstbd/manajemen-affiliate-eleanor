<?php

namespace App\Console\Commands;

use App\Imports\ProductUpdateImport;
use App\Models\ProductImportQueue;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessProductImportQueue extends Command
{
    protected $signature   = 'product:process-import-queue';
    protected $description = 'Proses antrian import produk dari file Excel yang sudah diupload';

    public function handle()
    {
        $pendingItems = ProductImportQueue::where('status', 'PENDING')->get();

        if ($pendingItems->isEmpty()) {
            return;
        }

        // Tandai semua sebagai PROCESSING sekaligus
        ProductImportQueue::whereIn('id', $pendingItems->pluck('id'))
            ->update(['status' => 'PROCESSING']);

        $adminIds   = $pendingItems->pluck('admin_id')->unique();
        $hasFailure = false;

        foreach ($pendingItems as $item) {
            try {
                if (!Storage::exists($item->file_path)) {
                    throw new \Exception("File tidak ditemukan: {$item->file_path}");
                }

                Excel::import(new ProductUpdateImport, $item->file_path);

                $item->update(['status' => 'DONE']);
            } catch (\Exception $e) {
                $hasFailure = true;
                $item->update([
                    'status'        => 'FAILED',
                    'error_message' => $e->getMessage(),
                ]);
                $this->error("Gagal proses file {$item->file_path}: {$e->getMessage()}");
            }
        }

        $admins = User::whereIn('id', $adminIds)->get();
        Notification::send($admins, new ImportFinishedNotification());

        $this->info("Selesai memproses {$pendingItems->count()} file import produk.");
    }
}