<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\UpdateRoleRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view('pages.users.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = User::select(['id', 'name', 'email', 'role', 'created_at']);

            return DataTables::of($query)
                ->addColumn('action', function($row) {
                    return view('pages.users.action-buttons', compact('row'))->render();
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action']) 
                ->make(true);
        }
    }

    public function store(CreateUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->userService->createUser($validated);
            return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah pengguna.');
        }
    }

    public function updateRole(UpdateRoleRequest $request, User $user)
    {
        $validated = $request->validated();
        
        $this->userService->changeUserRole($user, $validated['role']);

        return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui!');
    }
    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
            return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }
}