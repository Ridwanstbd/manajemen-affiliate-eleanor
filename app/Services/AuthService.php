<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService{
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user', 
        ]);

        event(new Registered($user));

        return $user;
    }
    public function checkUsernameStatus(string $username): array
    {
        $user = User::where('username',$username)->first();
        if (is_null($user->password)|| !$user->isclaimed){
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