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
    @include('partials.onboarding-stepper', ['currentStep' => 3])

<div class="flex flex-col lg:flex-row gap-12">

      <!-- Left: form -->
      <div class="flex-1">
        <h2 class="text-white text-sm font-bold tracking-wide mb-6">
    REQUIRED DOCUMENTS
</h2>

@if ($errors->any())
    <div class="mb-4 rounded bg-red-500/20 border border-red-400 text-red-200 px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('hr.onboarding.storeStep3') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6">

    @csrf

    <!-- Birth Certificate -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Birth Certificate <span class="text-red-400">*</span>
        </label>

        <div class="relative">
           <input type="file" name="birth_certificate" required
                accept=".pdf,.jpg,.jpeg,.png"
                class="w-[1335px] h-[45px] bg-[#0D1730] text-white text-sm rounded px-3 outline-none cursor-pointer"
            />
        </div>
        @error('birth_certificate')
            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <!-- Curriculum Vitae -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Curriculum Vitae <span class="text-red-400">*</span>
        </label>

        <div class="relative">
            <input type="file" name="curriculum_vitae" required
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                class="w-[1335px] h-[45px] bg-[#0D1730] text-white text-sm rounded px-3 outline-none cursor-pointer"
            />
        </div>
        @error('curriculum_vitae')
            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <!-- Valid ID -->
    <div>
        <label class="block text-slate-300 text-xs mb-1">
            Valid ID <span class="text-red-400">*</span>
        </label>

        <div class="relative">
           <input type="file" name="valid_id" required
                accept=".pdf,.jpg,.jpeg,.png"
                class="w-[1335px] h-[45px] bg-[#0D1730] text-white text-sm rounded px-3 outline-none cursor-pointer"
            />
        </div>
        @error('valid_id')
            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <!-- Navigation Buttons -->
    <!-- Back Button -->
    <a href="{{ route('hr.onboarding.step2') }}"
   class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold px-6 py-2.5 rounded shadow transition">
    BACK
</a>

    <!-- Next Button -->
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

</body>
</html>
