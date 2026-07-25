<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\HrEmployeeProfileProvisioner;

class AuthController extends Controller
{
    public function __construct(private readonly HrEmployeeProfileProvisioner $hrEmployeeProfileProvisioner)
    {
    }

    public function login(Request $request)
    {
        // 1. Validate the inputs
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        // 2. Use Auth::attempt() instead of manual plain-text comparison
        // This automatically hashes the input and checks it against the database
        $itsmCredentials = ['username' => $credentials['username'], 'password' => $credentials['password']];
        if (str_contains($credentials['username'], '@')) {
            $itsmCredentials = ['email' => $credentials['username'], 'password' => $credentials['password']];
        }

        if (Auth::attempt($itsmCredentials)) {
            
            // Regenerate session to prevent session fixation attacks (Best Practice)
            $request->session()->regenerate();
            
            // TODO: Fire a 'UserLoggedIn' event for the ITSM audit trail

            $user = Auth::user();
            $destination = $user->role === 'company_admin'
                ? $this->companyAdminDestination($user)
                // Keep the root-admin portal at the application root even
                // when the preceding request came from a mounted module.
                : $request->getSchemeAndHttpHost().'/admin/itsm/registration';

            // Do not allow a stale intended URL (for example, an admin page
            // visited before login) to override the portal assigned by role.
            return redirect()->to($destination);
        }

        $hrLogin = $this->hrEmployeeProfileProvisioner->authenticateHrAccount($credentials['username'], $credentials['password']);

        if ($hrLogin['success']) {
            // HR employees use their own session identity. A previously
            // authenticated ITSM account in the same browser must never carry
            // into a module session or remain usable through a direct URL.
            Auth::logout();
            $request->session()->regenerate();

            if ($hrLogin['requires_password_change']) {
                return redirect()->route('hr.first-login.password');
            }

            $this->signInEcommerceAdmin();

            return redirect()->to($this->employeeModuleDestination());
        }

        return back()->withErrors(['username' => $hrLogin['message']]);
    }

    public function showHrFirstLoginPassword()
    {
        abort_unless(session('hr_password_change_employee_id'), 403);

        return view('auth.first-login-password');
    }

    public function storeHrFirstLoginPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])\S+$/'],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, number, special character, and no spaces.',
        ]);

        abort_unless($this->hrEmployeeProfileProvisioner->completeFirstHrLogin($validated['password']), 403);

        $request->session()->regenerate();

        $this->signInEcommerceAdmin();

        return redirect()->to($this->employeeModuleDestination());
    }

    private function companyAdminDestination($user): string
    {
        $company = $user->company_id ? \App\Models\Company::find($user->company_id) : null;

        if ($company && $this->isExistingClientCompany($company)) {
            return route('client.itsm.employees');
        }

        if ($company && ! $company->setup_completed_at) {
            return route('newuser.show');
        }

        if ($company && (! $company->hr_employee_id || ! $this->hrEmployeeProfileProvisioner->hasEmployeeForCompany($company, (int) $company->hr_employee_id))) {
            return route('newuser.show', ['stage' => 3]);
        }

        return route('client.itsm.employees');
    }

    /**
     * Companies created before the ITSM onboarding flow can be identified by
     * either their existing onboarding marker/HR manager pointer or HR-owned
     * employee records. A rollback may leave the HR record unavailable, but
     * that must not send an established client through onboarding again.
     *
     * A genuinely new company has none of these markers, so it still begins
     * the normal onboarding flow.
     */
    private function isExistingClientCompany(\App\Models\Company $company): bool
    {
        if ($company->setup_completed_at || $company->hr_employee_id) {
            return true;
        }

        $employees = $this->hrEmployeeProfileProvisioner->employeesForCompany($company);
        if ($employees->isEmpty()) {
            return false;
        }

        $hrManager = $employees->first(fn (object $employee): bool => strcasecmp((string) $employee->department, 'Human Resources') === 0)
            ?? $employees->first();

        $company->forceFill([
            'hr_employee_id' => (int) $hrManager->id,
            'setup_completed_at' => now(),
        ])->save();

        return true;
    }

    /**
     * Resolve a module from both the employee's department and position.
     * Some HR records use "Shipping" as a department while others keep the
     * department as "Order Management" and store Shipping Coordinator only
     * in the position field. Both are Order Fulfillment accounts.
     */
    private function employeeModuleDestination(): string
    {
        $department = strtolower((string) session('employee_department', ''));
        $position = strtolower((string) session('employee_position', ''));
        $assignment = $department.' '.$position;

        if (str_contains($assignment, 'inventory') || str_contains($assignment, 'warehouse')) {
            return route('inventory.index');
        }

        if (str_contains($assignment, 'procurement') || str_contains($assignment, 'purchasing')) {
            return route('procurement.dashboard');
        }

        if (str_contains($assignment, 'fulfillment') || str_contains($assignment, 'operations') || str_contains($assignment, 'order') || str_contains($assignment, 'shipping')) {
            return route('order-fulfillment.dashboard');
        }

        if (str_contains($assignment, 'manufacturing') || str_contains($assignment, 'production')) {
            return route('manufacturing.dashboard');
        }

        if (str_contains($assignment, 'finance') || str_contains($assignment, 'accounting')) {
            return route('finance.dashboard');
        }

        if (
            str_contains($assignment, 'business intelligence')
            || str_contains($assignment, 'business analytics')
            || preg_match('/(^|\s)bi(\s|$)/', $assignment)
        ) {
            return route('bi.dashboard');
        }

        if (
            str_contains($assignment, 'e-commerce')
            || str_contains($assignment, 'ecommerce')
            || str_contains($assignment, 'electronic commerce')
            || str_contains($assignment, 'crm')
        ) {
            return $this->ecommerceAdminUrl();
        }

        return route('hr.dashboard');
    }

    private function signInEcommerceAdmin(): void
    {
        $employeeId = (int) session('employee_id');
        $clientId = (int) session('employee_client_id');

        $admin = $employeeId && $clientId
            ? \Modules\Ecommerce\Models\EcommerceAdmin::query()
                ->whereKey($employeeId)
                ->where('client_id', $clientId)
                ->first()
            : null;

        if ($admin && $admin->isEcommerceEmployee()) {
            Auth::guard('ecommerce_admin')->login($admin);
        }
    }

    private function ecommerceAdminUrl(): string
    {
        return url('/ecommerce-admin');
    }
}
