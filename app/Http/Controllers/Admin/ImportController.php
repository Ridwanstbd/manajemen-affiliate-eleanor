<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Services\Admin\ImportService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function getImportData()
    {
        return view('pages.admin.import-xlsx');
    }

    public function importData(ImportRequest $request): JsonResponse
    {
        try {
            $this->importService->executeBulkImport($request->allFiles());

            return response()->json([
                'status' => 'success',
                'message' => '5 File Excel berhasil diimport dengan rentang data diekstrak dari nama file.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }
}