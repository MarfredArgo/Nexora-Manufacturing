<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ServiceTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(string $portal = 'client', string $ticketType = 'erp_module')
    {
        $query = ServiceTicket::query()->latest();

        if ($portal === 'admin') {
            $query->where('ticket_type', 'nexora_support')->where('category', '!=', 'Password Reset');
        } else {
            $query->where('company_id', Auth::user()->company_id);
            if ($ticketType === 'client_password_reset') {
                $query->where('category', 'Password Reset')
                    ->whereIn('ticket_type', ['client_password_reset', 'nexora_support']);
            } else {
                $query->where('ticket_type', $ticketType);
            }
        }

        return view('service.service', [
            'portal' => $portal,
            'active' => 'service-desk',
            'ticketType' => $portal === 'admin' ? 'nexora_support' : $ticketType,
            'canCreateTicket' => $portal === 'client' && $ticketType === 'nexora_support',
            'canUpdateTicket' => $portal === 'admin' || ($portal === 'client' && in_array($ticketType, ['erp_module', 'client_password_reset'], true)),
            'canProcessPasswordResets' => $portal === 'client' && $ticketType === 'client_password_reset',
            'updateMode' => $portal === 'client' && $ticketType === 'erp_module' ? 'status_only' : ($ticketType === 'client_password_reset' ? 'password_reset' : 'full'),
            'title' => $this->titleFor($portal, $ticketType),
            'subtitle' => $this->subtitleFor($portal, $ticketType),
            'tickets' => $query->get(),
        ]);
    }

    public function supportIndex()
    {
        return $this->index('client', 'client_password_reset');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()->company_id) {
            abort(403);
        }

        if ($request->input('ticket_type') !== 'nexora_support') {
            abort(403, 'System admins cannot create ERP module tickets.');
        }

        $validated = $this->validatedNewSupportTicket($request);
        $company = $this->companyForRequest($request);

        ServiceTicket::create($validated + [
            'company_id' => $company?->id,
            'created_by' => Auth::id(),
            'client_name' => $company?->company_name,
            'ticket_no' => $this->nextTicketNo(),
            'ticket_type' => 'nexora_support',
            'status' => 'Open',
        ]);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function update(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        $this->authorizePortalAccess($ticket);

        if (Auth::user()->company_id) {
            if ($ticket->ticket_type !== 'erp_module') {
                abort(403, 'System admins can only view Nexora support ticket statuses.');
            }

            $ticket->update($this->validatedResolution($request));

            return back()->with('success', 'Ticket status updated successfully.');
        }

        $ticket->update($this->validatedTicket($request));

        return back()->with('success', 'Ticket updated successfully.');
    }

    private function validatedNewSupportTicket(Request $request): array
    {
        return $request->validate([
            'requester' => ['nullable', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function validatedTicket(Request $request): array
    {
        return $request->validate([
            'requester' => ['nullable', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function validatedResolution(Request $request): array
    {
        return $request->validate([
            'status' => ['required', 'string', 'in:In Progress,Resolved,Closed'],
        ]);
    }

    private function companyForRequest(Request $request): ?Company
    {
        if (Auth::user()->company_id) {
            return Company::find(Auth::user()->company_id);
        }

        return null;
    }

    private function authorizePortalAccess(ServiceTicket $ticket): void
    {
        if (Auth::user()->company_id && $ticket->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        if (! Auth::user()->company_id && $ticket->ticket_type !== 'nexora_support') {
            abort(403);
        }
    }

    private function nextTicketNo(): string
    {
        $nextId = (int) ServiceTicket::max('id') + 1;

        return 'NX-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    private function titleFor(string $portal, string $ticketType): string
    {
        if ($portal === 'admin') {
            return 'Nexora Support Desk';
        }

        return $ticketType === 'nexora_support'
            ? 'Ask Nexora Support'
            : ($ticketType === 'client_password_reset' ? 'Account Recovery' : 'ERP Module Tickets');
    }

    private function subtitleFor(string $portal, string $ticketType): string
    {
        if ($portal === 'admin') {
            return 'Support requests sent by company system admins to the Nexora root admin team.';
        }

        return $ticketType === 'nexora_support'
            ? 'Create tickets for Nexora root admins when your company needs platform-level help, then track their status.'
            : ($ticketType === 'client_password_reset'
                ? 'Password-reset requests for employees of your client. Set a temporary password, then provide it securely to the requester.'
                : 'Review and resolve tickets raised by ERP modules such as HR, Business Intelligence, Finance, and Operations.');
    }
}
