<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function changeUserRole(User $user, string $newRole): bool
    {
        $user->role = $newRole;
        return $user->save();
    }
    
    public function getUsersByRole(string $role)
    {
        return User::where('role', $role)->get();
    }
}