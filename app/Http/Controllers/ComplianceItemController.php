<?php

namespace App\Http\Controllers;

use App\Models\ComplianceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceItemController extends Controller
{
    public function index()
    {
        return view('compliance', [
            'items' => ComplianceItem::where('company_id', Auth::user()->company_id)
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ComplianceItem::create($this->validatedItem($request) + [
            'company_id' => Auth::user()->company_id,
        ]);

        return back()->with('success', 'Compliance item created successfully.');
    }

    public function update(Request $request, ComplianceItem $compliance): RedirectResponse
    {
        $this->authorizeCompanyAccess($compliance);
        $compliance->update($this->validatedItem($request));

        return back()->with('success', 'Compliance item updated successfully.');
    }

    private function validatedItem(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function authorizeCompanyAccess(ComplianceItem $compliance): void
    {
        if ($compliance->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
