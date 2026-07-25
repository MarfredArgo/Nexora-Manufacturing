<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermitController extends Controller
{
    public function index(Request $request)
    {
        // Baseline records are now empty by default
        $basePermits = [];

        // Process form submission to session store
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'issuer' => 'required|string|max:255',
                'expiry_date' => 'required|date',
                'status' => 'required|string'
            ]);

            $sessionPermits = session()->get('added_permits', []);

            $statusColor = 'bg-green-600';
            if ($request->input('status') === 'Expiring Soon') {
                $statusColor = 'bg-amber-500';
            } elseif ($request->input('status') === 'Expired') {
                $statusColor = 'bg-red-600';
            }

            $sessionPermits[] = [
                'title' => $request->input('title'),
                'issuer' => $request->input('issuer'),
                'expiry' => 'Expires: ' . date('Y-m-d', strtotime($request->input('expiry_date'))),
                'status' => $request->input('status'),
                'status_color' => $statusColor
            ];

            session()->put('added_permits', $sessionPermits);
            return redirect()->route('client.itsm.permit');
        }

        $sessionPermits = session()->get('added_permits', []);
        $allPermits = collect(array_merge($basePermits, $sessionPermits));

        // Compute live metrics dynamically based on available items
        $activeCount = $allPermits->where('status', 'Active')->count();
        $expiredCount = $allPermits->where('status', 'Expired')->count();
        $expiringSoonCount = $allPermits->where('status', 'Expiring Soon')->count();

        // Handle text search filters
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = strtolower($request->search);
            $allPermits = $allPermits->filter(function ($permit) use ($searchTerm) {
                return str_contains(strtolower($permit['title']), $searchTerm) ||
                       str_contains(strtolower($permit['issuer']), $searchTerm);
            });
        }

        // Handle dropdown filter selection
        $currentStatus = $request->get('status', 'All');
        if ($currentStatus !== 'All') {
            $allPermits = $allPermits->filter(function ($permit) use ($currentStatus) {
                return $permit['status'] === $currentStatus;
            });
        }

        return view('permit', [
            'permits' => $allPermits,
            'currentStatus' => $currentStatus,
            'activeCount' => $activeCount,
            'expiredCount' => $expiredCount,
            'expiringSoonCount' => $expiringSoonCount,
            'search' => $request->get('search', '')
        ]);
    }
}