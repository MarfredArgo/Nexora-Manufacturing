<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ServiceTicket;
use App\Models\User;
use App\Services\ErpIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['username' => ['required', 'string', 'max:255']]);
        $identifier = trim($validated['username']);
        $company = null;
        $employee = null;

        $user = User::query()
            ->where(fn ($query) => $query->where('email', $identifier)->orWhere('username', $identifier))
            ->first();
        if ($user?->company_id) {
            $company = Company::find($user->company_id);
        }

        if (! $user) {
            $employee = DB::connection('hr')->table('employees')
                ->where(fn ($query) => $query->where('company_email', $identifier)->orWhere('email', $identifier))
                ->first();
            $company = $employee?->client_id ? Company::find($employee->client_id) : null;
        }

        if ($company && ($user || ($employee && strtolower((string) ($employee->approval_status ?? 'Active')) === 'active'))) {
            $ticket = ServiceTicket::create([
                'company_id' => $company?->id,
                'ticket_no' => 'NX-'.str_pad((string) ((int) ServiceTicket::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'ticket_type' => 'client_password_reset',
                'requester' => $identifier,
                'client_name' => $company?->company_name,
                'module' => $user ? 'ITSM' : 'HR',
                'category' => 'Password Reset',
                'priority' => 'High',
                'status' => 'Open',
                'subject' => 'Password reset request',
                'description' => 'Requested from the public sign-in page.',
            ]);

            app(ErpIntegrationService::class)->recordAudit((int) $company->id, 'password.reset_requested', 'ITSM', [
                'ticket_id' => $ticket->id,
                'requester' => $identifier,
                'module' => $ticket->module,
            ]);
        }

        return back()->with('status', 'If the account exists, a password-reset request has been sent to its client ITSM Account Recovery queue.');
    }

    public function process(ServiceTicket $ticket): RedirectResponse
    {
        abort_unless(in_array($ticket->ticket_type, ['client_password_reset', 'nexora_support'], true) && $ticket->category === 'Password Reset', 404);
        abort_unless(Auth::user()?->role === 'company_admin' && Auth::user()?->company_id === $ticket->company_id, 403);
        abort_if($ticket->status === 'Resolved', 422, 'This password-reset request has already been resolved.');

        $validated = request()->validate([
            'temporary_password' => ['required', 'string', 'min:10', 'max:128', 'confirmed'],
        ]);
        $identifier = $ticket->requester;
        $password = $validated['temporary_password'];
        $user = User::query()
            ->where('company_id', $ticket->company_id)
            ->where(fn ($query) => $query->where('email', $identifier)->orWhere('username', $identifier))
            ->first();

        if ($user) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        } else {
            $employee = DB::connection('hr')->table('employees')
                ->where('client_id', $ticket->company_id)
                ->where(fn ($query) => $query->where('company_email', $identifier)->orWhere('email', $identifier))
                ->where('approval_status', 'Active')
                ->first();

            if (! $employee) {
                return back()->withErrors(['ticket' => 'The linked account is unavailable or no longer active.']);
            }

            DB::connection('hr')->table('employees')->where('id', $employee->id)->update([
                'temporary_password' => Hash::make($password),
                'must_change_password' => true,
                'updated_at' => now(),
            ]);
        }

        $ticket->update(['status' => 'Resolved']);
        app(ErpIntegrationService::class)->recordAudit((int) $ticket->company_id, 'password.reset_resolved', 'ITSM', [
            'ticket_id' => $ticket->id,
            'requester' => $identifier,
            'module' => $ticket->module,
        ]);

        return back()->with('reset_credentials', [
            'username' => $identifier,
            'password' => $password,
        ]);
    }
}
