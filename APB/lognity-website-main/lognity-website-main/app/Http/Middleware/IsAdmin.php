<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek jika user login DAN role-nya Admin atau Superadmin
        if (auth()->check() && in_array(auth()->user()->role, ['Admin', 'Superadmin'])) {
            return $next($request);
        }
        abort(403, 'Akses Ditolak.');
    }
}
