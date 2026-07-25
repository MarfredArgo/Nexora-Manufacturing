<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    /**
     * Ipakita ang Compliance tracking requirements kasama ang search at filter features.
     */
    public function index(Request $request)
    {

        // Kung walang laman ang session, i-initialize ito bilang isang empty array placeholder
        if (!session()->has('compliance_items')) {
            session(['compliance_items' => []]);
        }

        $items = collect(session('compliance_items'));

        // 1. Search Query execution engine matching (titling strings case-insensitive check)
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $items = $items->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['title']), $search) || 
                       str_contains(strtolower($item['audience']), $search);
            });
        }

        // 2. Status selection drop filter execution matching
        if ($request->filled('status')) {
            $items = $items->where('status', $request->status);
        }

        return view('compliance', ['requirements' => $items]);
    }

    /**
     * I-store ang bagong gawang raw requirement sa session array container storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'audience' => 'required|string|max:255',
            'status' => 'required|string',
            'progress' => 'required|integer|between:0,100',
        ]);

        // I-set ang tamang tailwind color scheme configuration mapping base sa orihinal na structure
        $colorMap = [
            'Active' => 'bg-[#16A34A]',
            'Urgent' => 'bg-[#DC2626]',
            'Completed' => 'bg-[#16A34A]',
            'Pending Review' => 'bg-[#D97706]'
        ];

        $currentItems = session('compliance_items', []);

        // Gumawa ng panibagong element data framework node array block 
        $newItem = [
            'title' => $validated['title'],
            'audience' => $validated['audience'],
            'status' => $validated['status'],
            'progress' => $validated['progress'] . '%',
            'color' => $colorMap[$validated['status']] ?? 'bg-slate-600'
        ];

        // Isalang sa unahan ng card grid list arrays flow stack gamit ang unshift sequence pipeline
        array_unshift($currentItems, $newItem);
        session(['compliance_items' => $currentItems]);

        return redirect()->route('client.itsm.compliance')
                         ->with('success', 'Compliance requirement added successfully!');
    }
}