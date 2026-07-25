<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskController extends Controller
{
    /**
     * Ipakita ang Risk Register (Blangko sa simula para sa Prototyping)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');

        // Walang predefined data — blangkong listahan para sa UI prototype mode
        $mockRisks = [];

        $risks = collect($mockRisks);

        // Filter logic para sa text search box
        if ($search) {
            $risks = $risks->filter(function ($risk) use ($search) {
                return false !== stripos($risk->title, $search) || 
                       false !== stripos($risk->category, $search);
            });
        }

        // Filter logic para sa dropdown choices
        if ($statusFilter) {
            $risks = $risks->filter(function ($risk) use ($statusFilter) {
                return strtolower($risk->status) === strtolower($statusFilter);
            });
        }

        return view('risk', compact('risks', 'search', 'statusFilter'));
    }

    /**
     * Tatanggapin ang form mula sa modal pero mag-re-redirect lang nang walang sinesave sa DB
     */
    public function store(Request $request)
    {
        return redirect()->route('client.itsm.risk')->with('success', 'Risk successfully logged! (Prototype Mode)');
    }

    /**
     * Fake update action para sa modal
     */
    public function update(Request $request)
    {
        return redirect()->route('client.itsm.risk')->with('success', 'Risk updated successfully! (Prototype Mode)');
    }

    /**
     * Fake manage action para sa standalone view
     */
    public function manage($id)
    {
        $risk = (object)[
            'id' => $id,
            'title' => 'Prototype Risk #' . $id,
            'category' => 'General',
            'status' => 'In Progress',
            'progress' => 50,
            'last_reviewed' => now()->toDateString()
        ];
        
        return view('risk-manage', compact('risk'));
    }
}