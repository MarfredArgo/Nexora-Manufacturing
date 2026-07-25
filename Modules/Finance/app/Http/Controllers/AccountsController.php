<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Finance\Models\Account;

class AccountsController extends Controller
{
    public function index()
    {
        $accounts = Account::query()->orderBy('account_id')->get()->map(fn (Account $account) => [
            'id' => $account->account_id, 'name' => $account->name, 'number' => $account->account_id,
            'type' => $account->account_type, 'detail' => $account->detail_type, 'balance' => (float) $account->balance,
            'date' => optional($account->created_at)->format('M d, Y'), 'inactive' => false,
        ]);
        return view('finance::accountsdash', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'type' => 'required|string|max:100', 'detail' => 'nullable|string|max:255', 'balance' => 'required|numeric']);
        $account = Account::create(['name' => $data['name'], 'account_type' => $data['type'], 'detail_type' => $data['detail'] ?? null, 'balance' => $data['balance']]);
        return response()->json(['success' => true, 'account' => ['id' => $account->account_id, 'name' => $account->name, 'number' => $account->account_id, 'type' => $account->account_type, 'detail' => $account->detail_type, 'balance' => (float) $account->balance, 'date' => optional($account->created_at)->format('M d, Y'), 'inactive' => false]]);
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'type' => 'required|string|max:100', 'detail' => 'nullable|string|max:255', 'balance' => 'required|numeric']);
        $account->update(['name' => $data['name'], 'account_type' => $data['type'], 'detail_type' => $data['detail'] ?? null, 'balance' => $data['balance']]);
        return response()->json(['success' => true]);
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return response()->json(['success' => true]);
    }
}
