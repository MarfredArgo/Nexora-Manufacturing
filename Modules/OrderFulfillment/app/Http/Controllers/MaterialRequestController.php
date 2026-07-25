<?php

namespace Modules\OrderFulfillment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\OrderFulfillment\Models\MaterialRequest;

class MaterialRequestController extends Controller
{
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'req_number' => ['required', 'string', 'max:100'],
            'date_requested' => ['required', 'date'],
            'item' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'department' => ['nullable', 'string', 'max:255'],
            'requested_by' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:50'],
        ]);

        $clientId = (int) session('employee_client_id');
        $duplicate = DB::connection('order_fulfillment')->table('requisitions')
            ->where('client_id', $clientId)
            ->where('req_number', $validated['req_number'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'A material request with this number already exists. Please reopen the form and try again.',
            ], 409);
        }

        $materialRequest = MaterialRequest::create($validated);

        return response()->json([
            'success' => true,
            'id' => $materialRequest->id,
            'message' => 'Material request sent to Procurement.',
        ]);
    }
}
