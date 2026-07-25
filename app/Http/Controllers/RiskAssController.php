<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskAssController extends Controller
{
    /**
     * Display the Risk Assessment console dashboard workspace.
     */
    public function index()
    
    {
        // Empty placeholder dataset for the threat matrix array setup
        $risks = [];

        // Fetch session dataset if modifications were made, otherwise stick to initial empty setup
        if (!session()->has('stored_risks')) {
            session(['stored_risks' => $risks]);
        }

        return view('RiskAss', ['risks' => session('stored_risks')]);
    }

    /**
     * Persist a newly evaluated threat matrix entity within the active configuration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'risk_id' => 'required|string',
            'title' => 'required|string',
            'inherent_score' => 'required|numeric',
            'inherent_text' => 'required|string',
            'likelihood' => 'required|integer|between:1,5',
            'impact' => 'required|integer|between:1,5',
            'residual_score' => 'required|numeric',
            'residual_text' => 'required|string',
            'status' => 'required|string|in:Active,Mitigated,Pending Review'
        ]);

        $currentRisks = session('stored_risks', []);
        
        $validated['id'] = count($currentRisks) + 1;
        
        $currentRisks[] = $validated;
        
        session(['stored_risks' => $currentRisks]);

        return redirect()->route('client.itsm.risk.assessment')->with('success', 'Risk Assessment metric committed successfully.');
    }
}