<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Contoh penggunaan di route:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,petugas_sarpras')
     *   ->middleware('role:admin|petugas_sarpras') // alternatif
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Ambil nama role user saat ini
        $userRole = $request->user()->role->name ?? null;

        // Cek apakah role user ada di daftar role yang diizinkan
        if (! $userRole || ! in_array($userRole, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}