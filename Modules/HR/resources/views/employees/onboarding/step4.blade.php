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

<div class="flex flex-col lg:flex-row gap-12">

    
      <!-- Left: form -->
      <div class="flex-1">
        <!-- Step 4 Content -->
<h2 class="text-white text-sm font-bold tracking-wide mb-6">
    COMPANY POLICIES & ACKNOWLEDGMENT
</h2>

 <form 
action="{{ route('hr.onboarding.storeStep4') }}"
method="POST"
> @csrf

    @if ($errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-100 rounded p-4 mb-6">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 w-[1335px] bg-[#0D1730] rounded-xl px-8 py-5 border border-white/10">
        <p class="text-slate-400 text-xs uppercase tracking-wide mb-1">Company Email (auto-generated)</p>
        <p class="text-white text-sm font-semibold">{{ $companyEmailPreview }}</p>
        <p class="mt-1 text-[11px] text-slate-400">If another employee already has this name, a number is added after the last name.</p>
    </div>

<div class="w-[1335px] bg-[#0D1730] rounded-xl p-8 space-y-8">

    <!-- Policy 1 -->
    <div class="border-b border-white/10 pb-6">
        <h3 class="text-white font-semibold mb-3">
            Employee Handbook Acknowledgment
        </h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
            As part of the onboarding process, I acknowledge that I have read,
            understood, and agree to comply with the following company policies.
            I understand that failure to follow these policies may result in
            disciplinary action in accordance with company procedures.
        </p>

        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
           <input 
    type="checkbox" 
    name="policy_1"
    value="1"
    required
    class="mt-1 w-4 h-4"
>
            <span>
                I certify that the employee has read and signed the physical copy
                of the Company's Employee Handbook and has authorized Human
                Resources to record this acknowledgment electronically.
            </span>
        </label>
    </div>

    <!-- Policy 2 -->
    <div class="border-b border-white/10 pb-6">
        <h3 class="text-white font-semibold mb-3">Code of Conduct Sign-Off</h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
            I certify that the employee has read and signed the physical copy of the Company's Code of Conduct and has authorized Human Resources to record this acknowledgment electronically.
</p>
        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
            <input 
    type="checkbox" 
    name="policy_2"
    value="1"
    required
    class="mt-1 w-4 h-4"
>
            <span>
                I certify that the employee has read and signed the physical copy
                of the Company's Employee Handbook and has authorized Human
                Resources to record this acknowledgment electronically.
            </span>
        </label>
    </div>

    <!-- Policy 3 -->
    <div class="border-b border-white/10 pb-6">
        <h3 class="text-white font-semibold mb-3">Confidentiality & Non-Disclosure Agreement (NDA)</h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
           I understand that during my employment I may have access to confidential and proprietary information belonging to the Company, its customers, or its business partners. I agree to protect this information and not disclose, copy, or use it for unauthorized purposes during or after my employment, except as required to perform my assigned duties or as authorized by the Company.
        </p>

        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
            <input 
    type="checkbox" 
    name="policy_3"
    value="1"
    required
    class="mt-1 w-4 h-4"
>
            <span>
                I confirm that I have read, understood, and agree to comply with the Company's Confidentiality and Non-Disclosure Agreement (NDA), including my responsibility to protect confidential information.
            </span>
        </label>
    </div>

    <!-- Policy 4 -->
    <div class="border-b border-white/10 pb-6">
        <h3 class="text-white font-semibold mb-3">Health & Safety Policy Acknowledgment</h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
            I agree to comply with all workplace health and safety policies, procedures, and emergency protocols established by the Company. I understand that maintaining a safe working environment is a shared responsibility, and I will promptly report any hazards, unsafe conditions, accidents, or incidents.
        </p>

        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
            <input 
    type="checkbox" 
    name="policy_4"
    value="1"
    required
        class="mt-1 w-4 h-4"
>
            <span>
                I confirm that I have read, understood, and agree to follow the Company's Health and Safety policies, procedures, and workplace safety requirements.
            </span>
        </label>
    </div>

    <!-- Policy 5 -->
    <div class="border-b border-white/10 pb-6">
        <h3 class="text-white font-semibold mb-3">Anti-Harassment Policy Sign-Off</h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
            I understand that the Company is committed to providing a workplace that is free from harassment, discrimination, bullying, and retaliation. I agree to treat all individuals with dignity and respect and to report any inappropriate behavior through the appropriate reporting channels. I understand that violations of this policy may result in disciplinary action.
        </p>

        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
            <input 
    type="checkbox" 
    name="policy_5"
    value="1"
    required
    class="mt-1 w-4 h-4"
>
            <span>
               I confirm that I have read, understood, and agree to comply with the Company's Anti-Harassment Policy and will contribute to maintaining a respectful, safe, and inclusive workplace.
            </span>
        </label>
    </div>

    <!-- Policy 6 -->
    <div>
        <h3 class="text-white font-semibold mb-3">Policy Title 6</h3>

        <p class="text-slate-300 text-sm leading-7 mb-4">
            As part of the onboarding process, I acknowledge that I have read,
            understood, and agree to comply with the following company policies.
            I understand that failure to follow these policies may result in
            disciplinary action in accordance with company procedures.
        </p>

        <label class="flex items-start gap-3 text-slate-300 text-sm cursor-pointer">
            <input 
    type="checkbox" 
    name="policy_6"
    value="1"
    required
 class="mt-1 w-4 h-4"
>
            <span>
                I certify that the employee has read and signed the physical copy
                of the Company's Employee Handbook and has authorized Human
                Resources to record this acknowledgment electronically.
            </span>
        </label>
    </div>

   
</div>

<!-- Navigation Buttons -->

 



<div class="pt-6 flex gap-4">

<a href="{{ route('hr.onboarding.step3') }}"
class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold px-6 py-2.5 rounded shadow transition">
BACK
</a>


<button type="submit"
class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded shadow-lg shadow-blue-900/40 transition">

FINISH

</button>

</div>


</form>
  </div>
  


    </div>

    

   </div>

</body>
</html>
