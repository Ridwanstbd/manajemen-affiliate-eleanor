<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized. Silakan login terlebih dahulu.');
        }

        if (!$request->user()->hasRole($roles)) {
            $role = strtoupper($request->user()->role ?? '');

            if ($role === 'AFFILIATOR') {
                return redirect()->route('affiliator.index')
                    ->with('warning', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            if ($role === 'ADMINISTRATOR' || $role === 'ADMIN') {
                return redirect()->route('admin-dashboard.dashboard')
                    ->with('warning', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            abort(403, 'Forbidden. Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}