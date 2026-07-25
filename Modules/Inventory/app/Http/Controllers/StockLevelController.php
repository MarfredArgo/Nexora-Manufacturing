<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\StockLevel;
use Illuminate\Http\Request;

class StockLevelController extends Controller
{
    public function update(Request $request, StockLevel $stockLevel)
    {
        $request->validate(['reorder_threshold' => 'required|integer|min:0']);
        $stockLevel->update(['reorder_threshold' => $request->input('reorder_threshold')]);

        return back()->with('success', 'Reorder threshold updated.');
    }
}

