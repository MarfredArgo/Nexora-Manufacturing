<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $clientId = (int) $request->user()->company_id;
        $allAudits = Schema::hasTable('erp_audit_logs')
            ? DB::table('erp_audit_logs')->where('client_id', $clientId)->latest('created_at')->get()->map(function ($log): array {
                return [
                    'id' => '#ERP-'.str_pad((string) $log->id, 6, '0', STR_PAD_LEFT).': '.str_replace('.', ' ', ucfirst($log->event)),
                    'scope' => ucfirst($log->module),
                    'auditor' => 'ERP Integration',
                    'date' => \Illuminate\Support\Carbon::parse($log->created_at)->format('F d, Y H:i'),
                    'score' => 'Recorded',
                    'status' => str_ends_with($log->event, 'failed') ? 'Failed' : 'Completed',
                    'status_class' => str_ends_with($log->event, 'failed') ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800',
                ];
            })
            : collect([]);

        // 2. Save to session if a new audit is created via modal form submission
        if ($request->isMethod('post')) {
            $newAudits = session()->get('added_audits', []);
            
            // Build a new dynamic database row mock from user input fields
            $newAudits[] = [
                'id' => '#AUD-2026-0' . (count($allAudits) + count($newAudits) + 1) . ': ' . $request->input('title'),
                'scope' => $request->input('scope'),
                'auditor' => $request->input('auditor'),
                'date' => date('F d, Y', strtotime($request->input('date'))),
                'score' => '—',
                'status' => 'Scheduled',
                'status_class' => 'bg-blue-100 text-blue-800'
            ];

            session()->put('added_audits', $newAudits);
            return redirect()->route('client.itsm.audit');
        }

        // Combine base collection and dynamically session-appended list rows
        $addedAudits = session()->get('added_audits', []);
        $audits = collect(array_merge($allAudits->toArray(), $addedAudits));

        // --- METRICS CALCULATION (Calculated from the total pool before filtering) ---
        $upcomingCount = $audits->where('status', 'Scheduled')->count();
        $totalInspections = $audits->count();
        $failedCount = $audits->where('status', 'Failed')->count();
        // ----------------------------------------------------------------------------

        // 3. Apply structural Search filter queries if requested by user
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = strtolower($request->search);
            $audits = $audits->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['id']), $searchTerm) ||
                       str_contains(strtolower($item['scope']), $searchTerm) ||
                       str_contains(strtolower($item['auditor']), $searchTerm);
            });
        }

        // 4. Apply analytical Status constraints matching view request queries
        $currentStatus = $request->get('status', 'All');
        if ($currentStatus !== 'All') {
            $audits = $audits->filter(function ($item) use ($currentStatus) {
                return $item['status'] === $currentStatus;
            });
        }

        // Send counts directly alongside your main collection items array matrix
        return view('audit', compact('audits', 'currentStatus', 'upcomingCount', 'totalInspections', 'failedCount'));
    }
}
