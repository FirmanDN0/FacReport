<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->isTechnician() && !auth()->user()->isAdmin())) {
            abort(403, 'Akses ditolak. Anda bukan teknisi.');
        }

        return $next($request);
    }
}
