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
        // Pastikan user sudah login
        if (!$request->user()) {
            abort(403, 'Unauthorized. Silakan login terlebih dahulu.');
        }

        // Cek apakah user memiliki salah satu dari role yang diizinkan
        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Forbidden. Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}