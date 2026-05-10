<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserService;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $UserService)
    {
        $this->userService = $UserService;
    }
    public function index(Request $request)
    {
        $tab = $request->query('tab','request-access');
                                     
        $data = $this->userService->getTabData($tab, $request);
        $viewData = array_merge([
            'currentTab' => $tab,
        ], $data);
        return view('pages.admin.users.index',$viewData);
    }

    public function activeData(Request $request)
    {
        if ($request->ajax()){
            $data = $this->userService->getTabData('active', $request);
            $users = collect($data['users']);
            return DataTables::of($users)
                ->addColumn('action', function($row) {
                    return view('pages.admin.users.active.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function requestAccessData(Request $request)
    {
        if ($request->ajax()){
            $data = $this->userService->getTabData('request-access', $request); 
            
            $users = collect($data['users']);
            return DataTables::of($users)
                ->addColumn('action', function($row) {
                    return view('pages.admin.users.request-access.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function blacklistData(Request $request)
    {
        if ($request->ajax()){
            $data = $this->userService->getTabData('blacklist', $request);
            $users = collect($data['users']);
            return DataTables::of($users)
                ->addColumn('action', function($row) {
                    return view('pages.admin.users.blacklist.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function kolContractData(Request $request)
    {
        if ($request->ajax()){
            $data = $this->userService->getTabData('kol-contract',$request);
            $users = collect($data['users']);
            return DataTables::of($users)
                ->addColumn('action', function($row) {
                    return view('pages.admin.users.kol-contract.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function storeKOLContract(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_fee' => 'required|numeric',
            'required_video_count' => 'required|integer',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            $this->userService->createKOLContract($request->all());
            return redirect()->back()->with('success', 'Berhasil mendaftarkan KOL dan membuat kontrak baru.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses kontrak: ' . $e->getMessage());
        }
    }
    public function extendKOLContract(Request $request)
    {
        $request->validate([
            'original_contract_id' => 'required|exists:kol_contracts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_fee' => 'required|numeric',
            'required_video_count' => 'required|integer',
        ]);

        try {
            $this->userService->extendKOLContract($request->all());
            return redirect()->back()->with('success', 'Kontrak KOL berhasil diperpanjang.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperpanjang kontrak: ' . $e->getMessage());
        }
    }

    public function approveAccess(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:system_access_requests,id'
        ]);

        try {
            $this->userService->approveAccess($request);
            
            return redirect()->back()->with('success', 'Akses berhasil disetujui. Akun affiliator otomatis terbuat dengan password bawaan: password123');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui akses: ' . $e->getMessage());
        }
    }

    public function rejectAccess(Request $request)
    {
        $request->validate([
            'id' => 'required|string', 
        ]);

        try {
            $this->userService->rejectAccess($request->id);
            return redirect()->back()->with('success', 'Permintaan akses berhasil ditolak.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak permintaan: ' . $e->getMessage());
        }
    }

    public function storeBlacklist(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'violation_reason' => 'required|string|max:1000',
        ]);

        try {
            $this->userService->addToBlacklist($request->all());
            return redirect()->back()->with('success', 'Affiliator berhasil dimasukkan ke daftar hitam.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses daftar hitam: ' . $e->getMessage());
        }
    }
    public function restoreBlacklist(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        try {
            $this->userService->restoreBlacklist($request->id);
            return redirect()->back()->with('success', 'Akun affiliator berhasil dipulihkan dari daftar hitam.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memulihkan akun: ' . $e->getMessage());
        }
    }
}
