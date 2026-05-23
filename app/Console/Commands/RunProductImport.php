<?php

namespace App\Console\Commands;

use App\Imports\ProductUpdateImport;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class RunProductImport extends Command
{
    protected $signature   = 'product:run-import';
    protected $description = 'Proses antrian import produk dari file JSON queue';

    public function handle()
    {
        $queueFile = storage_path('app/private/import-queue.json');

        if (!file_exists($queueFile)) {
            $this->info('Tidak ada antrian.');
            return;
        }

        $queue = json_decode(file_get_contents($queueFile), true);

        if (empty($queue)) {
            $this->info('Antrian kosong.');
            return;
        }

        file_put_contents($queueFile, json_encode([]));

        $notifyAdminIds = [];

        foreach ($queue as $item) {
            $adminId = $item['admin_id'];
            $paths   = $item['paths'];

            foreach ($paths as $path) {
                try {
                    $fullPath = Storage::disk('local')->path($path);

                    if (!file_exists($fullPath)) {
                        Log::warning("RunProductImport: file tidak ditemukan: {$fullPath}");
                        continue;
                    }

                    Excel::import(new ProductUpdateImport, $fullPath);

                    $this->info("Berhasil: {$path}");

                } catch (Throwable $e) {
                    Log::error('RunProductImport gagal', [
                        'path'  => $path,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $this->error("Gagal [{$path}]: " . $e->getMessage());
                } finally {
                    Storage::disk('local')->delete($path);
                }
            }

            $notifyAdminIds[] = $adminId;
        }

        if (!empty($notifyAdminIds)) {
            $admins = User::whereIn('id', array_unique($notifyAdminIds))->get();
            Notification::send($admins, new ImportFinishedNotification());
            $this->info('Notifikasi terkirim ke ' . $admins->count() . ' admin.');
        }
    }
}