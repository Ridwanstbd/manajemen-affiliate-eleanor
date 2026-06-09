<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Redirect user berdasarkan role jika sudah login.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $role = strtoupper($user->role ?? '');

                if ($role === 'ADMINISTRATOR' || $role === 'ADMIN') {
                    return redirect()->route('admin-dashboard.dashboard');
                }

                if ($role === 'AFFILIATOR') {
                    return redirect()->route('affiliator.index');
                }

                return redirect('/affiliator');
            }
        }

        return $next($request);
    }
}
