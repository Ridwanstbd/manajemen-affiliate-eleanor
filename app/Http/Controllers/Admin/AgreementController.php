<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Services\Admin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AgreementController extends Controller
{
    protected $agreementService;

    public function __construct(Admin\AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function index()
    {
        return view('pages.admin.agreements.index');
    }

    public function getData()
    {
        $query = Agreement::select('id','content','is_active','updated_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function($row) {
                return view('components.atoms.badge', [
                    'slot' => $row->is_active ? 'Aktif' : 'Non-Aktif',
                    'status' => $row->is_active ? 'paid' : 'unpaid'
                ])->render();
            })
            ->editColumn('updated_at', function($row) {
                    return \Carbon\Carbon::parse($row->start_date)->format('d M Y');
                })
            ->addColumn('action', function($row) {
                return view('pages.admin.agreements.action-buttons', compact('row'))->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'   => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $this->agreementService->createAgreement($validated);

        return redirect()->back()->with('success', 'Persetujuan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Agreement $agreement)
    {
        $validated = $request->validate([
            'content'   => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $this->agreementService->updateAgreement($agreement, $validated);

        return redirect()->back()->with('success', 'Persetujuan berhasil diperbarui!');
    }

    public function destroy(Agreement $agreement)
    {
        $this->agreementService->deleteAgreement($agreement);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}