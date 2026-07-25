<?php

namespace Modules\Procurement\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcurementAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('nexora.root_admin_module_testing') && $request->user()?->role === 'root_admin') {
            return $next($request);
        }

        $department = strtolower((string) session('employee_department', ''));

        if (! session('employee_logged_in') || ! session('employee_client_id') || ! (str_contains($department, 'procurement') || str_contains($department, 'purchasing'))) {
            return redirect()->route('login')->withErrors([
                'username' => 'Sign in with an approved Procurement employee account to access Procurement.',
            ]);
        }

        return $next($request);
    }
}
