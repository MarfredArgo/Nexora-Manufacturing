<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskAssessmentController extends Controller
{
    public function index()
    {
        return view('risk', [
            'risks' => RiskAssessment::where('company_id', Auth::user()->company_id)
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        RiskAssessment::create($this->validatedRisk($request) + [
            'company_id' => Auth::user()->company_id,
        ]);

        return back()->with('success', 'Risk assessment created successfully.');
    }

    public function update(Request $request, RiskAssessment $risk): RedirectResponse
    {
        $this->authorizeCompanyAccess($risk);
        $risk->update($this->validatedRisk($request));

        return back()->with('success', 'Risk assessment updated successfully.');
    }

    private function validatedRisk(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:50'],
            'owner' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'review_date' => ['nullable', 'date'],
            'mitigation_plan' => ['nullable', 'string'],
        ]);
    }

    private function authorizeCompanyAccess(RiskAssessment $risk): void
    {
        if ($risk->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
