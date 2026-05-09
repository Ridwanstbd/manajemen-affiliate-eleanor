<?php

namespace App\Services\Admin;

use App\Imports\ProductUpdateImport;
use App\Models\ImportHistory;
use App\Imports\CoreMetricsImport;
use App\Imports\CreatorListImport;
use App\Imports\LiveListImport;
use App\Imports\ProductListImport;
use App\Imports\VideoListImport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    public function executeBulkImport(array $files)
    {
        return DB::transaction(function () use ($files) {
            $sampleFile = $files['file_core_metrics'];
            $filename = $sampleFile->getClientOriginalName();
            
            preg_match('/_(\d{8})-(\d{8})\.xlsx$/', $filename, $matches);

            if (!isset($matches[1]) || !isset($matches[2])) {
                throw new \Exception('Gagal mengekstrak tanggal dari format nama file.');
            }

            $startDate = Carbon::createFromFormat('Ymd', $matches[1])->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('Ymd', $matches[2])->format('Y-m-d');

            $batch = ImportHistory::firstOrCreate(
                [
                    'start_date' => $startDate,
                    'end_date'   => $endDate
                ],
                [
                    'admin_id'    => auth()->id() ?? 1, 
                    'import_date' => now(),
                ]
            );

            $importMapping = [
                'file_core_metrics' => CoreMetricsImport::class,
                'file_creator_list' => CreatorListImport::class,
                'file_live_list'    => LiveListImport::class,
                'file_product_list' => ProductListImport::class,
                'file_video_list'   => VideoListImport::class,
            ];

            foreach ($importMapping as $key => $importClass) {
                if (isset($files[$key])) {
                    Excel::import(new $importClass($batch->id), $files[$key]);
                }
            }

            return $batch;
        });
    }

    public function executeProductUpdateImport($files)
    {
        foreach ($files as $file) {
            Excel::import(new ProductUpdateImport, $file);
        }
    }
}