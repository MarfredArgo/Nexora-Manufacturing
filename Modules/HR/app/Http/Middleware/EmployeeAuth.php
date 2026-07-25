<?php

namespace Modules\HR\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\HR\Models\Employee;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('employee_logged_in')) {
            return redirect()->route('login');
        }

        $employee = Employee::query()->find(session('employee_id'));
        if (! $employee || ! in_array(strtolower((string) $employee->approval_status), ['active'], true)) {
            session()->forget([
                'employee_logged_in', 'employee_role', 'employee_id', 'employee_name',
                'employee_email', 'employee_department', 'employee_client_id',
            ]);

            return redirect()->route('login')->withErrors([
                'username' => 'This employee account is no longer active. Contact your client system administrator.',
            ]);
        }

        $role = session('employee_role');
        $department = strtolower(trim(session('employee_department', '')));

        if ($role === 'employee' && $department !== 'human resources') {
            if (! $request->routeIs('hr.employee.dashboard') && ! $request->routeIs('hr.logout')) {
                return redirect()->route('hr.employee.dashboard');
            }
        }

        return $next($request);
    }
}
