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
    @include('partials.onboarding-stepper', ['currentStep' => 4])

<!-- Final Step Content -->
<div class="flex-1">

    <h2 class="text-white text-sm font-bold tracking-wide mb-6">
        EMPLOYEE INFORMATION
    </h2>

    <form class="space-y-6">

        <!-- Employee ID -->
        <div>
            <label class="block text-slate-300 text-xs mb-1">
                Employee ID
            </label>

            <div class="relative">
                <input
                    type="text"
                    value="{{ $employee['employee_id'] }}"
    readonly
                    class="w-[650px] h-[45px] bg-[#132B52] text-white text-sm rounded px-4 pr-12 border border-blue-500/30 cursor-not-allowed"
                />

                <!-- Copy Icon -->
                <button
                    type="button"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-300 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <rect x="9" y="9" width="11" height="11" rx="2" stroke-width="2"/>
                        <rect x="4" y="4" width="11" height="11" rx="2" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Email & Password Row -->
        <div class="flex gap-6">

            <!-- Employee Email -->
            <div>
                <label class="block text-slate-300 text-xs mb-1">
                    Employee Email
                </label>

                <input
                    type="email"
                   value="{{ $employee['company_email'] }}"
    readonly
                    class="w-[650px] h-[45px] bg-[#132B52] text-white text-sm rounded px-4 border border-blue-500/30 cursor-not-allowed"
                />
            </div>

            <!-- Temporary Password -->
            <div>
                <label class="block text-slate-300 text-xs mb-1">
                    Temporary Password (pending ITSM approval)
                </label>

                <input
                    type="text"
                       value="{{ $employee['temporary_password'] }}"
                    readonly
                    class="w-[650px] h-[45px] bg-[#132B52] text-white text-sm rounded px-4 border border-blue-500/30 cursor-not-allowed"
                />
            </div>

        </div>

        <!-- Dashboard Button -->
        <div class="pt-8">
            <a 
href="{{ route('hr.dashboard') }}"
class="inline-flex items-center bg-[#3B82F6] hover:bg-[#2563EB] text-white font-semibold px-8 py-3 rounded shadow-lg shadow-blue-900/40 transition">

    DASHBOARD

</a>
        </div>

    </form>

</div>

     

    </div>
  </div>
  


    </div>

   </div>

</body>
</html>
