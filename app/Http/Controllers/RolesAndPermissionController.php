<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;


class RolesAndPermissionController extends Controller
{


public function index()
{
    $roles = Role::withCount('users')->get();
    return view('users.rolesandpermission', compact('roles'));
}



    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            Role::whereIn('id', $ids)->delete();
            return redirect()->route('users.roles')->with('success', 'Selected roles deleted successfully.');
        }

        return redirect()->route('users.roles')->with('error', 'No roles selected for deletion.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->route('users.roles')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('users.roles')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('users.roles')->with('success', 'Role deleted successfully.');
    }
}
