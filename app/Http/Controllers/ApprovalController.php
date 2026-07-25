<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ApprovalController extends Controller
{
    public function bulkHandle(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action_type'); // approve or reject

        if (!empty($ids)) {
            if ($action === 'approve') {
                User::whereIn('id', $ids)->update(['status' => 'approved']);
                return redirect()->route('users.pending')->with('success', 'Selected users approved.');
            } elseif ($action === 'reject') {
                User::whereIn('id', $ids)->update(['status' => 'rejected']);
                return redirect()->route('users.pending')->with('success', 'Selected users rejected.');
            }
        }

        return redirect()->route('users.pending')->with('error', 'No users selected.');
    }
}
