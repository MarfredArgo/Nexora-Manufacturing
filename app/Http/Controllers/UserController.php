<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\HrEmployeeProfileProvisioner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly HrEmployeeProfileProvisioner $hrEmployeeProfileProvisioner,
    )
    {
    }


    public function clients()
    {
        $companies = Company::with('adminUser')->orderByDesc('created_at')->get();

        return view('users.index', [
            'users' => $companies,
            'portal' => 'admin',
            'active' => 'clients',
            'title' => 'Client Management',
            'entityLabel' => 'client',
            'entityLabelPlural' => 'clients',
            'primaryIdLabel' => 'Client ID',
        ]);
    }

    public function employees()
    {
        $company = $this->clientCompany();
        $employees = $company ? $this->hrEmployeeProfileProvisioner->employeesForCompany($company) : collect();

        return view('users.index', [
            'users' => $employees,
            'portal' => 'client',
            'active' => 'employees',
            'title' => 'Employee Management',
            'entityLabel' => 'employee',
            'entityLabelPlural' => 'employees',
            'primaryIdLabel' => 'Employee ID',
        ]);
    }

    public function pendingApprovals()
    {
        $company = $this->clientCompany();
        $employees = $company
            ? $this->hrEmployeeProfileProvisioner->employeesForCompany($company)
                ->filter(fn (object $employee): bool => $employee->status === 'Pending')
                ->values()
            : collect();

        return view('users.index', [
            'users' => $employees,
            'portal' => 'client',
            'active' => 'pending-approvals',
            'title' => 'Pending Approvals',
            'entityLabel' => 'approval',
            'entityLabelPlural' => 'approvals',
            'primaryIdLabel' => 'Employee ID',
        ]);
    }

    public function updateEmployee(Request $request, int $employee): RedirectResponse
    {
        $company = $this->clientCompany();
        abort_unless($company, 403);
        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive,Pending,Suspended'],
        ]);

        $currentEmployee = $this->hrEmployeeProfileProvisioner->findEmployeeForCompany($company, $employee);
        abort_unless($currentEmployee, 404);

        if ($currentEmployee->status === 'Pending' && $validated['status'] === 'Active') {
            return redirect()
                ->route('client.itsm.pending-approvals')
                ->withErrors(['status' => 'Approve the HR manager from Pending Approvals to create their login credentials.']);
        }

        $this->hrEmployeeProfileProvisioner->updateEmployeeForCompany($company, $employee, $validated);

        return redirect()
            ->route('client.itsm.employees')
            ->with('success', 'Employee updated successfully.');
    }

    public function approveHrManager(int $employee): RedirectResponse
    {
        $company = $this->clientCompany();
        abort_unless($company, 403);

        $manager = $this->hrEmployeeProfileProvisioner->findEmployeeForCompany($company, $employee);
        abort_unless(
            $manager && $manager->status === 'Pending',
            404
        );

        if ($manager->department !== 'Human Resources' || $manager->username) {
            $this->hrEmployeeProfileProvisioner->approveEmployeeForCompany($company, $employee);

            return redirect()
                ->route('client.itsm.pending-approvals')
                ->with('success', 'Employee approved. They will be required to change their password on first sign-in.');
        }

        $password = Str::password(16, symbols: true);
        $provisioned = $this->hrEmployeeProfileProvisioner->provisionApprovedHrManager($company, $manager, $password);

        $company->update(['hr_employee_id' => $provisioned['employee_id']]);

        return redirect()
            ->route('client.itsm.pending-approvals')
            ->with('success', 'HR manager approved and login credentials generated.')
            ->with('hr_credentials', [
                'username' => $provisioned['email'],
                'password' => $password,
            ]);
    }

    private function clientCompany(): ?Company
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'company_admin') {
            return null;
        }

        return Company::find($user->company_id);
    }


     public function index()
    {
        return $this->employees();
    }

 public function pending()
{
    $pendingUsers = User::where('status', 'pending')->get();
    return view('users.pending', compact('pendingUsers'));
}
}
