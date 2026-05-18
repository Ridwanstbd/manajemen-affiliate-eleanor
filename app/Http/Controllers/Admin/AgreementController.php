<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AgreementRequest;
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

    public function store(AgreementRequest $request)
    {
        $this->agreementService->createAgreement($request->validated());

        return redirect()->back()->with('success', 'Persetujuan baru berhasil ditambahkan!');
    }

    public function update(AgreementRequest $request, Agreement $agreement)
    {
        $this->agreementService->updateAgreement($agreement, $request->validated());

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