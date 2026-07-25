<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'company_admin' || ! $request->user()->company_id) {
            abort(403, 'Client system administrator access is required.');
        }

        return $next($request);
    }
}
