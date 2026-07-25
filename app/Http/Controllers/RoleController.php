<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        // Handle saving the role here
        // Example:
        // Role::create($request->all());
        return redirect()->back()->with('success', 'Role created successfully!');
    }
}
