<?php

namespace App\Services;

use App\Models\SystemAccessRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService{
    public function register(array $data): SystemAccessRequest
    {
        $user = SystemAccessRequest::create([
            'tiktok_username' => $data['tiktok_username'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
        ]);

        return $user;
    }
    public function claim(string $username, array $data)
    {
        $user = User::where('username', $username)->first();
        $user->update([
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password'])
        ]);
        return $user;
    }
    public function checkUsernameStatus(string $username): array
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return [
                'status' => 'undefined',
                'message' => 'Akun tidak ditemukan. Silakan ajukan akses.',
                'action' => 'redirect_to_request_access',
                'data' => ['username' => $username]
            ];
        }
        if (is_null($user->password) || !$user->is_claimed){
            return [
                'status' => 'unclaimed',
                'message' => 'Akun ditemukan, namun belum diklaim. Silakan buat password.',
                'action' => 'redirect_to_claim_form',
                'data' => ['username' => $username]
            ];
        }
        return [
            'status' => 'ready_to_login',
            'message' => 'Username valid. Silakan masukkan password.',
            'action' => 'redirect_to_password_input', 
            'data' => ['username' => $username]
        ];
    }
    public function authenticate(string $username, string $password): bool
    {
        return Auth::attempt([
            'username' => $username,
            'password' => $password
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
    }
    public function sendResetLink(array $data): string
    {
        return Password::sendResetLink($data);
    }

    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
    }
}