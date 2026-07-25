<?php

namespace Modules\HR\Http\Controllers;

use Modules\HR\Http\Controllers\Concerns\ResolvesPerPage;
use Modules\HR\Http\Controllers\Concerns\RespondsWithAjaxList;
use Modules\HR\Models\Employee;
use Illuminate\Http\Request;
use Modules\HR\Models\Department;

class DepartmentController extends Controller
{
    use ResolvesPerPage;
    use RespondsWithAjaxList;

    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('department_name', 'like', '%' . $search . '%')
                  ->orWhere('department_code', 'like', '%' . $search . '%');
            });
        }

        $departments = $query->orderBy('department_name')->get();

        return view('departments.index', compact('departments'));
    }


    public function show($slug, Request $request)
    {
        $map = [
            'business-intelligence' => 'Business Intelligence',
            'finance' => 'Finance',
            'human-resources' => 'Human Resources',
            'it' => 'IT Service Management',
            'inventory' => 'Inventory Management',
            'e-commerce' => 'E-commerce',
             'order' => 'Order Management',
           'procurement' => 'Procurement Management',
               'production' => 'Production Management',
        ];

        $departmentName = $map[$slug] ?? null;

        if (!$departmentName) {
            abort(404);
        }

        $query = Employee::where('department', $departmentName);

        // Search by employee ID or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        // Sorting (matches the sort dropdown in the view)
        switch ($request->input('sort')) {
            case 'name_asc':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'position_asc':
                $query->orderBy('position', 'asc');
                break;
            case 'position_desc':
                $query->orderBy('position', 'desc');
                break;
            case 'newest':
                $query->orderBy('hire_date', 'desc');
                break;
            case 'oldest':
                $query->orderBy('hire_date', 'asc');
                break;
            default:
                $query->orderBy('id', 'asc');
                break;
        }

        $departments = $query
            ->paginate($this->perPage($request))
            ->withQueryString();

        if ($this->wantsAjaxList($request)) {
            return $this->ajaxListResponse(
                'departments.partials.show-results',
                compact('departments')
            );
        }

        return view('departments.show', compact(
            'departments',
            'departmentName'
        ));
    }
}
