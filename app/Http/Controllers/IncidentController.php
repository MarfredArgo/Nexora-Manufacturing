<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Helper method to initialize an empty workspace data array in the session.
     */
    private function getPrototypeData(Request $request)
    {
        // Prototype page should start empty (no seeded data).
        // If you already logged incidents in this session, keep them; otherwise return an empty collection.
        // NOTE: This intentionally does NOT auto-seed any sample/default incidents.
        $prototype = $request->session()->get('prototype_incidents');
        return collect(is_array($prototype) ? $prototype : []);
    }

    /**
     * Display the dynamic workplace canvas listing with search and status filtering.
     */
    public function index(Request $request)
    {
        $allIncidents = $this->getPrototypeData($request);
        $search = $request->input('search');
        $statusFilter = $request->input('status'); // Captured the status pill selection

        // 1. Apply Search Query Filter if present
        if (!empty($search)) {
            $allIncidents = $allIncidents->filter(function ($item) use ($search) {
                return (isset($item['title']) && false !== stripos($item['title'], $search)) || 
                       (isset($item['id']) && false !== stripos($item['id'], $search)) ||
                       (isset($item['reporter']) && false !== stripos($item['reporter'], $search));
            });
        }

        // 2. Apply Status Quick Filter if present
        if (!empty($statusFilter) && in_array($statusFilter, ['Open', 'Investigating', 'Resolved'])) {
            $allIncidents = $allIncidents->where('status', $statusFilter);
        }

        // Calculate metrics dynamically based on global data scope
        $masterData = $this->getPrototypeData($request);
        $criticalCount = $masterData->where('severity', 'Critical')->whereNotIn('status', ['Resolved'])->count();
        $totalCount = $masterData->count(); 
        $avgResolutionTime = $totalCount > 0 ? "2.4 Hours" : "N/A"; 

        return view('incident', [
            'incidents' => $allIncidents,
            'criticalCount' => $criticalCount,
            'totalThisMonth' => $totalCount,
            'avgResolutionTime' => $avgResolutionTime,
            'currentSearch' => $search,
            'currentStatus' => $statusFilter // Passed back to maintain active UI element states
        ]);
    }

    /**
     * Push a new entry dynamically into the user's session storage array.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'description' => 'nullable|string',
        ]);

        $currentIncidents = $request->session()->get('prototype_incidents', []);
        
        $customId = 'INC-2026-' . str_pad(count($currentIncidents) + 1, 3, '0', STR_PAD_LEFT);

        array_unshift($currentIncidents, [
            'id' => $customId,
            'title' => $validated['title'],
            'severity' => $validated['severity'],
            'datetime' => now()->format('Y-m-d H:i:s'),
            'reporter' => Auth::user()->name ?? 'Guest Engineer',
            'status' => 'Open'
        ]);

        $request->session()->put('prototype_incidents', $currentIncidents);

        return redirect()->route('client.itsm.risk.incident')
            ->with('success', "Prototype Incident {$customId} added locally!");
    }

    /**
     * Alter incident record status parameters in session memory layout.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Open,Investigating,Resolved']);
        
        $currentIncidents = $request->session()->get('prototype_incidents', []);
        
        foreach ($currentIncidents as &$incident) {
            if ($incident['id'] === $id) {
                $incident['status'] = $request->status;
                break;
            }
        }
        
        $request->session()->put('prototype_incidents', $currentIncidents);
        
        return redirect()->route('client.itsm.risk.incident', array_filter([
            'status' => $request->input('current_status_context'), // retains context after quick edit
            'search' => $request->input('current_search_context')
        ]))->with('success', "Incident {$id} updated to {$request->status}.");
    }
}