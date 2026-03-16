<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
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
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function updateRole(UpdateRoleRequest $request, User $user)
    {
        $validated = $request->validated();
        
        $this->userService->changeUserRole($user, $validated['role']);

        return redirect()->back()->with('success', 'Role pengguna berhasil diperbarui!');
    }
}