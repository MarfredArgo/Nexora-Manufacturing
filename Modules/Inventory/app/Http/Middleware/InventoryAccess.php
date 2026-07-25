<?php

namespace Modules\Inventory\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InventoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('nexora.root_admin_module_testing') && $request->user()?->role === 'root_admin') {
            return $next($request);
        }

        if (! session('employee_logged_in') && ! session('employee_client_id')) {
            return redirect()->route('login')->withErrors([
                'username' => 'Sign in with your approved HR employee account to access Inventory.',
            ]);
        }

        $position = strtolower((string) session('employee_position', ''));
        $department = strtolower((string) session('employee_department', ''));

        if (! str_contains($department, 'inventory') && ! str_contains($position, 'warehouse')) {
            return redirect()->route('login')->withErrors([
                'username' => 'Your account does not have Inventory access.',
            ]);
        }

        return $next($request);
    }
}
