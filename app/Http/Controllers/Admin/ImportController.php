<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Models\ImportHistory; 
use App\Services\Admin\ImportService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function getImportData()
    {
        return view('pages.admin.import-xlsx.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = ImportHistory::select(['id', 'start_date', 'end_date', 'admin_id', 'import_date'])
                                  ->orderBy('import_date', 'desc');

            return DataTables::of($query)
                ->addColumn('admin_name', function($row) {
                    return $row->admin->username ?? 'System'; 
                })
                ->editColumn('import_date', function($row) {
                    return \Carbon\Carbon::parse($row->import_date)->format('d M Y, H:i');
                })
                ->editColumn('start_date', function($row) {
                    return \Carbon\Carbon::parse($row->start_date)->format('d M Y');
                })
                ->editColumn('end_date', function($row) {
                    return \Carbon\Carbon::parse($row->end_date)->format('d M Y');
                })
                ->make(true);
        }
    }

    public function importData(ImportRequest $request)
    {
        try {
            $this->importService->executeBulkImport($request->allFiles());
            return redirect()->back()->with('success', '5 File Excel berhasil diimport dengan rentang data diekstrak dari nama file.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}