<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Services\Admin\AgreementService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AgreementController extends Controller
{
    protected $agreementService;

    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function index()
    {
        return view('pages.admin.agreements.index');
    }

    public function getData()
    {
        $query = Agreement::with('user')->select('id', 'user_id', 'content', 'is_active', 'is_kol', 'updated_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('target', function($row) {
                if ($row->user_id) {
                    return '<span style="color: var(--primary-blue); font-weight: 600;">Personal: @' . ($row->user->username ?? 'User Terhapus') . '</span>';
                }
                return $row->is_kol 
                    ? '<span style="color: #d97706; font-weight: 600;">Global KOL</span>' 
                    : '<span style="color: #059669; font-weight: 600;">Global Reguler</span>';
            })
            ->addColumn('status', function($row) {
                return view('components.atoms.badge', [
                    'slot' => $row->is_active ? 'Aktif' : 'Non-Aktif',
                    'status' => $row->is_active ? 'paid' : 'unpaid'
                ])->render();
            })
            ->editColumn('content', function($row) {
                $stripped = strip_tags($row->content);
                return strlen($stripped) > 80 ? substr($stripped, 0, 80) . '...' : $stripped;
            })
            ->editColumn('updated_at', function($row) {
                return \Carbon\Carbon::parse($row->updated_at)->translatedFormat('d M Y');
            })
            ->addColumn('action', function($row) {
                return view('pages.admin.agreements.action-buttons', compact('row'))->render();
            })
            ->rawColumns(['status', 'action', 'target'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_kol'] = $request->has('is_kol');
        $data['user_id'] = null; 

        $this->agreementService->createAgreement($data);

        return redirect()->back()->with('success', 'Persetujuan global baru berhasil ditambahkan!');
    }

    public function update(Request $request, Agreement $agreement)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['is_kol'] = $request->has('is_kol');

        $this->agreementService->updateAgreement($agreement, $data);

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