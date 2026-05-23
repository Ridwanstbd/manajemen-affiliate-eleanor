<?php

namespace App\Console\Commands;

use App\Imports\ProductUpdateImport;
use App\Models\ProductImportQueue;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessProductImportQueue extends Command
{
    protected $signature   = 'product:process-import-queue';
    protected $description = 'Proses antrian import produk dari file Excel yang sudah diupload';

    public function handle()
    {
        $pendingItems = ProductImportQueue::where('status', 'PENDING')->get();

        if ($pendingItems->isEmpty()) {
            $this->info('Tidak ada antrian PENDING.');
            return;
        }

        ProductImportQueue::whereIn('id', $pendingItems->pluck('id'))
            ->update(['status' => 'PROCESSING']);

        $adminIds = $pendingItems->pluck('admin_id')->unique();

        foreach ($pendingItems as $item) {
            try {
                if (!Storage::disk('local')->exists($item->file_path)) {
                    throw new \Exception("File tidak ditemukan di storage: {$item->file_path}");
                }

                $fullPath = Storage::disk('local')->path($item->file_path);

                Excel::import(new ProductUpdateImport, $fullPath);

                $item->update(['status' => 'DONE']);
                $this->info("Berhasil: {$item->file_path}");

            } catch (Throwable $e) {
                $item->update([
                    'status'        => 'FAILED',
                    'error_message' => $e->getMessage(),
                ]);
                Log::error("ProcessProductImportQueue gagal", [
                    'item_id'   => $item->id,
                    'file_path' => $item->file_path,
                    'error'     => $e->getMessage(),
                    'trace'     => $e->getTraceAsString(),
                ]);
                $this->error("Gagal [{$item->id}]: {$e->getMessage()}");
            }
        }

        $admins = User::whereIn('id', $adminIds)->get();
        Notification::send($admins, new ImportFinishedNotification());

        $this->info("Selesai memproses {$pendingItems->count()} file import produk.");
    }
}