<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureReviewer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'reviewer') {
            abort(403, 'Halaman ini hanya dapat diakses oleh reviewer website.');
        }

        return $next($request);
    }
}
