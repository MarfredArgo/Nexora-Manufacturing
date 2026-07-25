<?php

namespace App\Http\Controllers; // Tiyaking nakaturo sa tamang directory folder mo

use Illuminate\Http\Request;

class RiskMitigationController extends Controller
{
    /**
     * Ipakita ang listahan ng Mitigation Plans (Prototype Mode - Blangko)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');

        // 1. Blangkong listahan para sa UI prototype mode (Walang predefined data)
        $mockPlans = [];

        $mitigations = collect($mockPlans);

        // Filter para sa search bar (kung sakaling may laman)
        if ($search) {
            $mitigations = $mitigations->filter(function ($plan) use ($search) {
                return false !== stripos($plan->title, $search) || 
                       false !== stripos($plan->owner, $search);
            });
        }

        // Filter para sa status dropdown (kung sakaling may laman)
        if ($statusFilter) {
            $mitigations = $mitigations->filter(function ($plan) use ($statusFilter) {
                return strtolower($plan->status) === strtolower($statusFilter);
            });
        }

        // 2. Analytics Metrics - Naka-set sa zero/default values para malinis ang simula
        $activeCount = 0;
        $totalBudget = 0.00;
        $overdueCount = 0;

        // 3. Mock Risks na magagamit pa rin bilang pagpipilian sa iyong Create Modal dropdown
        $risks = [
            (object)['id' => 101, 'title' => 'System Downtime Threat'],
            (object)['id' => 102, 'title' => 'Data Loss Vulnerability'],
            (object)['id' => 103, 'title' => 'Phishing Attack Vector'],
        ];

        return view('mitigation', compact(
            'mitigations', 
            'search', 
            'statusFilter', 
            'activeCount', 
            'totalBudget', 
            'overdueCount', 
            'risks'
        ));
    }

    /**
     * Fake store/create action para sa modal form
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('success', 'Mitigation Plan successfully logged! (Prototype Mode)');
    }
}