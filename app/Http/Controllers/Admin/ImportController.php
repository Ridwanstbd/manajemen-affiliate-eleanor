<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Models\ImportHistory;

use App\Imports\CoreMetricsImport;
use App\Imports\CreatorListImport;
use App\Imports\LiveListImport;
use App\Imports\ProductListImport;
use App\Imports\VideoListImport;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function getImportData()
    {
        view('pages.admin.import-xlsx');
    }

    public function importData(ImportRequest $request)
    {
        
        DB::beginTransaction();

        try {
            $sampleFilename = $request->file('file_core_metrics')->getClientOriginalName();
            preg_match('/_(\d{8})-(\d{8})\.xlsx$/', $sampleFilename, $matches);

            $startDate = Carbon::createFromFormat('Ymd', $matches[1])->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('Ymd', $matches[2])->format('Y-m-d');
            $batch = ImportHistory::firstOrCreate([
                'import_date' => now(),
                'start_date' => $startDate,
                'end_date'   => $endDate
            ]);
            $filesToImport = [
                'file_core_metrics' => CoreMetricsImport::class,
                'file_creator_list' => CreatorListImport::class,
                'file_live_list'    => LiveListImport::class,
                'file_product_list' => ProductListImport::class,
                'file_video_list'   => VideoListImport::class,
            ];
            foreach ($filesToImport as $inputKey => $importClass) {
                $file = $request->file($inputKey);
                Excel::import(new $importClass($batch->id), $file);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '5 File Excel berhasil diimport dengan rentang data diekstrak dari nama file.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }
}
