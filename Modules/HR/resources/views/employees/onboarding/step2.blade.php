<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Employee Onboarding</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

 @include('partials.navbar')
<body class="bg-[#1B3A6B] min-h-screen font-sans">

  <div class="pt-[140px]">
    <!-- Employee Onboarding Content -->

<div class="max-w-6xl mx-auto">

   <!-- Title -->
    <h1 class="text-white text-xl font-bold tracking-wide mb-8">EMPLOYEE ONBOARDING</h1>
    @include('partials.onboarding-stepper', ['currentStep' => 2])

<div class="flex flex-col lg:flex-row gap-12">

      <!-- Left: form -->
      <div class="flex-1">
        <h2 class="text-white text-sm font-bold tracking-wide mb-4">
    EMPLOYMENT INFORMATION
</h2>

@if ($errors->any())
    <div class="mb-4 rounded bg-red-500/20 border border-red-400 text-red-200 px-4 py-3 text-sm">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form 
 action="{{ route('hr.onboarding.storeStep2') }}"
    method="POST"
    class="space-y-6 max-w-3xl" >

    @csrf

    <!-- Top Row -->
    <!-- Top Row -->
<div class="flex gap-6">

    <!-- Department -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Department <span class="text-red-500">*</span>
        </label>
        <select id="department" name="department" required class="w-[350px] h-[40px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">Select Department</option>
            <option>Business Intelligence</option>
            <option>E-commerce</option>
            <option>Finance</option>
            <option>Human Resources</option>
            <option>IT Service Management</option>
            <option>Inventory Management</option>
            <option>Order Management</option>
            <option>Procurement Management</option>
            <option>Production Management</option>
        </select>
    </div>

    <!-- Position -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Position <span class="text-red-500">*</span>
        </label>
        <select id="position" name="position" required class="w-[350px] h-[40px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">Select Department First</option>
        </select>
    </div>

</div>

<!-- Bottom Row -->
<div class="flex gap-6">

    <!-- Hire Date -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Hire Date <span class="text-red-500">*</span>
        </label>
        <input
        name="hire_date"
            type="date"
            required
            value="{{ old('hire_date', session('step2.hire_date')) }}"
            class="w-[350px] h-[40px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500"
        />
    </div>

</div>

<div class="flex gap-6">
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Start Time <span class="text-red-500">*</span>
        </label>
        <input
            name="start_time"
            type="time"
            required
            value="{{ old('start_time', session('step2.start_time')) }}"
            class="w-[350px] h-[40px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500"
        />
        <p class="mt-1 text-[10px] text-slate-400">HR assigned work start (basis for late check)</p>
    </div>

    <div>
        <label class="block text-slate-300 text-xs mb-1">
            End Time <span class="text-red-500">*</span>
        </label>
        <input
            name="end_time"
            type="time"
            required
            value="{{ old('end_time', session('step2.end_time')) }}"
            class="w-[350px] h-[40px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500"
        />
        <p class="mt-1 text-[10px] text-slate-400">Required work hours = End Time âˆ’ Start Time</p>
    </div>
</div>

    <!-- Navigation Buttons -->
<div class="pt-6 flex gap-4">

    <!-- Back Button -->
    <a href="{{ route('hr.onboarding.step1') }}"
   class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold px-6 py-2.5 rounded shadow transition">
    BACK
</a>

    <button
    type="submit"
    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded shadow-lg shadow-blue-900/40 transition">
    NEXT

    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 8l4 4-4 4"/>
    </svg>
</button>

</div>
</form>
      </div>

     

    </div>
  </div>
  


    </div>

   </div>

<script>
  const positionsByDepartment = {
    "Business Intelligence": ["BI Manager", "BI Analyst", "Data Analyst", "Business Analyst"],
    "E-commerce": ["E-commerce Manager", "Marketplace Specialist", "Product Listing Specialist", "Digital Merchandiser", "SEO Specialist"],
    "Finance": ["Finance Manager", "Accountant", "Financial Analyst"],
    "Human Resources": ["HR Manager", "HR Officer", "Recruiter", "HR Assistant"],
    "IT Service Management": ["IT Manager", "System Administrator", "Network Administrator", "IT Support Specialist", "Software Developer"],
    "Inventory Management": ["Inventory Manager", "Inventory Controller", "Warehouse Staff", "Inventory Analyst"],
    "Order Management": ["Shipping Coordinator", "Returns Specialist", "Customer Service Representative"],
    "Procurement Management": ["Procurement Manager", "Purchasing Officer", "Vendor Coordinator"],
    "Production Management": ["Production Manager", "Production Supervisor", "Production Planner", "Production Staff"]
  };

  const departmentSelect = document.getElementById("department");
  const positionSelect = document.getElementById("position");

  departmentSelect.addEventListener("change", function () {
    const selectedDepartment = this.value;
    const positions = positionsByDepartment[selectedDepartment] || [];

    positionSelect.innerHTML = "";

    if (!selectedDepartment) {
      positionSelect.appendChild(new Option("Select Department First", ""));
      return;
    }

    positionSelect.appendChild(new Option("Select Position", ""));
    positions.forEach(function (position) {
      positionSelect.appendChild(new Option(position, position));
    });
  });
</script>

</body>
</html>
