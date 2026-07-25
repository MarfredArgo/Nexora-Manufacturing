<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Modules\HR\Models\Employee;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'company_email' => 'required',
            'password' => 'required',
        ]);

        $employee = Employee::where(
            'company_email',
            $request->company_email
        )->first();

        if (!$employee) {
            return back()->with('error', 'Invalid email or password. Please try again.');
        }

        if ($employee->temporary_password !== $request->password) {
            return back()->with('error', 'Invalid email or password. Please try again.');
        }

        $department = preg_replace('/[^a-z0-9]/', '', strtolower((string) $employee->department));
        $position = preg_replace('/[^a-z0-9]/', '', strtolower((string) $employee->position));
        $isHrManager = in_array($department, ['humanresources', 'hr'], true)
            && in_array($position, ['hrmanager', 'humanresourcesmanager'], true);
        $isBiEmployee = str_contains($department, 'businessintelligence')
            || str_contains($department, 'businessanalytics')
            || in_array($position, ['bimanager', 'bianalyst', 'dataanalyst', 'businessanalyst'], true);

        session([
            'employee_logged_in' => true,
            'employee_role' => $isHrManager ? 'admin' : 'employee',
            'employee_id' => $employee->id,
            'employee_code' => $employee->employee_id,
            'employee_name' => $employee->first_name,
            'employee_email' => $employee->company_email,
            'employee_department' => $employee->department,
            'employee_position' => $employee->position,
            'employee_client_id' => (int) $employee->client_id,
        ]);

        $route = $isHrManager
            ? 'hr.dashboard'
            : ($isBiEmployee ? 'bi.dashboard' : 'hr.employee.dashboard');

        return redirect()->route($route);
    }

    public function logout(Request $request)
    {
        session()->flush();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
