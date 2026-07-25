<?php

namespace Modules\Manufacturing\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManufacturingBomAccess
{
    /**
     * BOM definitions are manufacturing master data. E-commerce users may
     * select an approved BOM for a listing, but cannot view or change it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if (config('nexora.root_admin_module_testing') && $user?->role === 'root_admin') {
            return $next($request);
        }

        $assignment = strtolower(trim((string) session('employee_department').' '.(string) session('employee_position')));

        abort_unless(
            str_contains($assignment, 'manufacturing') || str_contains($assignment, 'production'),
            403,
            'Only Manufacturing employees can manage Bills of Materials.'
        );

        return $next($request);
    }
}
