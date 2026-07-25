<?php

namespace Modules\Finance\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FinanceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web') ?? Auth::guard('web')->user();

        if (config('nexora.root_admin_module_testing') && $user?->role === 'root_admin') {
            return $next($request);
        }

        if (! session('employee_logged_in') || ! session('employee_client_id')) {
            return redirect()->away($request->getSchemeAndHttpHost().'/login')->withErrors([
                'username' => 'Sign in with your approved HR employee account to access Finance.',
            ]);
        }

        return $next($request);
    }
}
