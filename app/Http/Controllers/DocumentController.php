<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display the Documents console dashboard workspace.
     */
    public function index(Request $request)
    {
        // Fetch session dataset if modifications were made, otherwise stick to an empty placeholder
        $documents = session('stored_documents', []);

        // Dynamic Filtering
        $currentFilter = $request->query('filter', 'All');
        if ($currentFilter !== 'All') {
            $documents = array_filter($documents, function ($doc) use ($currentFilter) {
                return isset($doc['status']) && $doc['status'] === $currentFilter;
            });
        }

        // Dynamic Search
        $search = $request->query('search');
        if (!empty($search)) {
            $documents = array_filter($documents, function ($doc) use ($search) {
                return (str_contains(strtolower($doc['details'] ?? ''), strtolower($search)) || 
                        str_contains(strtolower($doc['linked_id'] ?? ''), strtolower($search)) ||
                        str_contains(strtolower($doc['classification'] ?? ''), strtolower($search)));
            });
        }

        // Compute Metric Strip Totals based on base session data
        $baseDocs = session('stored_documents', []);
        $totalStored = count($baseDocs);
        $needsSignOff = count(array_filter($baseDocs, fn($d) => ($d['status'] ?? '') === 'Needs Sign-Off'));
        $lapsedCount = count(array_filter($baseDocs, fn($d) => ($d['status'] ?? '') === 'Lapsed'));

        return view('Document', [
            'documents' => $documents,
            'currentFilter' => $currentFilter,
            'totalStored' => $totalStored,
            'needsSignOff' => $needsSignOff,
            'lapsedCount' => $lapsedCount
        ]);
    }

    /**
     * Persist a newly uploaded document metric entity within the active configuration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'details' => 'required|string',
            'linked_id' => 'required|string',
            'classification' => 'required|string',
            'status' => 'required|string|in:Active,Needs Sign-Off,Lapsed'
        ]);

        $currentDocs = session('stored_documents', []);
        
        $validated['id'] = count($currentDocs) + 1;
        $currentDocs[] = $validated;
        
        session(['stored_documents' => $currentDocs]);

        return redirect()->route('client.itsm.document')->with('success', 'Document registered successfully.');
    }
}