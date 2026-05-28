<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Blokir akses catalog bagi user yang ada di tabel blacklist
     * dengan account_status = 'BANNED'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->account_status === 'BANNED' && $user->blacklists()->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda telah diblokir. Anda tidak dapat mengakses katalog.'
                ], 403);
            }

            return redirect()->route('affiliator.index')
                ->with('error', 'Akun Anda telah diblokir dan tidak dapat mengakses katalog produk.');
        }

        return $next($request);
    }
}