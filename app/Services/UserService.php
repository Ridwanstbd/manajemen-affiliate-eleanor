<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }
    public function changeUserRole(User $user, string $newRole): bool
    {
        $user->role = $newRole;
        return $user->save();
    }
    
    public function getUsersByRole(string $role)
    {
        return User::where('role', $role)->get();
    }
    public function deleteUser(User $user): bool
    {
        if (auth()->id() === $user->id) throw new \Exception('Tidak bisa menghapus akun sendiri.');
        
        return $user->delete();
    }
}