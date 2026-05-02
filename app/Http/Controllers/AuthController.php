<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UsernameRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showUsername()
    {
        return view('auth.check-username');
    }
    public function showPassword()
    {
        return view('auth.check-password');
    }
    public function verifyUsername(UsernameRequest $request)
    {
        $validatedData = $request->validated();
        $result = $this->authService->checkUsernameStatus($validatedData['username']);

        switch ($result['action']) {
            case 'redirect_to_request_access':
                return redirect()->route('access.request')->with('info', $result['message']);
                
            case 'redirect_to_claim_form':
                session(['claim_username' => $result['data']['username']]);
                return redirect()->route('account.claim')->with('info', $result['message']);
                
            case 'redirect_to_password_input':
                session(['login_username' => $result['data']['username']]);
                return redirect()->route('login.password')->with('info', $result['message']);
        }
    }
    public function verifyPassword(LoginRequest $request)
    {
        $username = session('login_username');
        if (!$username){
            return redirect()->route('login')->with('error', 'Sesi login tidak valid atau telah habis. Silakan masukkan username kembali.');
        }
        $validatedData = $request->validated();
        $password = $validatedData['password'];
        $isAuthenticated = $this->authService->authenticate($username,$password);
        if ($isAuthenticated){
            session()->forget('login_username');
            $request->session()->regenerate();
            $userRole = auth()->user()->role;

            if ($userRole === 'ADMIN' || $userRole === 'ADMINISTRATOR') {
                return redirect()->intended('/dashboard')->with('success', 'Selamat datang, Administrator!');
            } elseif ($userRole === 'AFFILIATOR') {
                return redirect()->intended('/affiliator')->with('success', 'Berhasil login! Selamat datang di dashboard Affiliator.');
            }
        }
        return back()->withErrors([
            'password' => 'Password yang Anda masukkan salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $status = $this->authService->sendResetLink($request->validated());

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->authService->resetPassword($request->validated());

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
