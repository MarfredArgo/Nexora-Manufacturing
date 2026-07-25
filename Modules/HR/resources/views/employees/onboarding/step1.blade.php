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
    @include('partials.onboarding-stepper', ['currentStep' => 1])

<div class="flex flex-col lg:flex-row gap-12">

      <!-- Left: form -->
      <div class="flex-1">
        <h2 class="text-white text-sm font-bold tracking-wide mb-4">PERSONAL INFORMATION</h2>

@if (session('error'))
    <div class="mb-4 rounded bg-red-500/20 border border-red-400 text-red-200 px-4 py-3 text-sm">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-500 text-white p-3 rounded">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

       <form
    id="onboarding-step1-form"
    action="{{ route('hr.onboarding.storeStep1') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-4 max-w-3xl">
    

    @csrf

          <!-- Name row -->
          <div class="flex items-start gap-4">

  <!-- First Name -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">First Name</label>
    <div class="relative">
     <input
    type="text"
    name="first_name"
    class="name-field w-[220px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 pr-8 outline-none focus:ring-1 focus:ring-blue-500"
      />
    </div>
  </div>

  <!-- Middle Name -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Middle Name</label>
    <div class="relative">
      <input
        type="text" name="middle_name"
        class="name-field w-[220px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 pr-8 outline-none focus:ring-1 focus:ring-blue-500"
      />
    </div>
  </div>

  <!-- Last Name -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Last Name</label>
    <div class="relative">
      <input
        type="text" name="last_name"
        class="name-field w-[220px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 pr-8 outline-none focus:ring-1 focus:ring-blue-500"
      />
    </div>
  </div>

  <!-- Suffix -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Suffix</label>
    <div class="relative">
      <select
        name="suffix"
        class="w-[118px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500 appearance-none"
      >
        <option class="hidden"></option>
        <option value="Jr.">Jr.</option>
        <option value="Sr.">Sr.</option>
        <option value="II">II</option>
        <option value="III">III</option>
        <option value="IV">IV</option>
        <option value="V">V</option>
      </select>
    </div>
  </div>

</div>

          <!-- Gender / Marital / Nationality row -->
       <div class="flex items-start gap-6">
            <div class="flex items-start gap-8">

  <!-- Gender -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Gender</label>
    <select  name="gender" class="w-[253px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500 appearance-none">
      <option class="hidden"></option>
    <option>Male</option>
      <option>Female</option>
      <option>Prefer not to say</option>
    </select>
  </div>

  <!-- Marital Status -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Marital Status</label>
    <select  name="marital_status" class="w-[253px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 outline-none focus:ring-1 focus:ring-blue-500 appearance-none">
      <option class="hidden"></option>
      <option>Single</option>
      <option>Married</option>
      <option>Widowed</option>
    </select>
  </div>

  <!-- Nationality -->
  <div>
    <label class="block text-slate-300 text-xs mb-1">Nationality</label>
    <div class="relative">
      <input
    type="text"
    name="nationality"
    class="name-field w-[253px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 pr-8 outline-none focus:ring-1 focus:ring-blue-500"
      />
    </div>
  </div>

</div>
</div>

          <!-- Address -->
          <div>
            <label class="block text-slate-300 text-xs mb-1">Address</label>
            <div class="relative">
              <input type="text"  name="address" class="w-[825px] bg-[#0d1730] text-white text-sm rounded px-3 py-2 pr-8 outline-none focus:ring-1 focus:ring-blue-500" />
           
            </div>
          </div>

          <!-- Email / Phone row -->
          <div class="flex items-start gap-6 pt-2">
            <div>
              <label class="block text-slate-300 text-xs mb-1">Email</label>
              <div class="relative">
                <input
                    type="email"
                    name="email"
                    id="email"
                    maxlength="254"
                    class="w-[452px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 py-2 pr-8 outline-none focus:ring-1 focus:ring-blue-500" />
               
              </div>
            </div>
            <div>
              <label class="block text-slate-300 text-xs mb-1">Phone Number</label>
              <div class="relative">
                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    inputmode="numeric"
                    maxlength="11"
                    class="w-[253px] h-[28px] bg-[#0d1730] text-white text-sm rounded px-3 py-2 pr-8 outline-none focus:ring-1 focus:ring-blue-500" />
                
              </div>
            </div>
          </div>

          <!-- Company Email Preview -->
          <div class="pt-2">
            <label class="block text-slate-300 text-xs mb-1">Company Email (auto-generated)</label>
            <div class="relative">
              <input
                  type="text"
                  id="company_email_preview"
                  readonly
                  value="{{ $companyEmailPreview ?? '' }}"
                  placeholder="Generated from first and last name"
                  class="w-[452px] h-[28px] bg-[#0d1730] text-slate-300 text-sm rounded px-3 py-2 outline-none border border-white/10" />
            </div>
            <p class="mt-1 text-[11px] text-slate-400">If the same name already exists, a number is added (e.g. johnsmith2@nexora.com).</p>
          </div>

          <!-- Next button -->
          <div class="pt-8">
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
       

        <!-- Right: profile picture upload -->
      <div class="w-full lg:w-[220px] flex flex-col items-center">
        <h2 class="text-white text-sm font-bold tracking-wide mb-4 self-start lg:self-center">PROFILE PICTURE</h2>

        <label for="profile_picture" class="cursor-pointer group">
            <div class="relative w-[160px] h-[160px] rounded-full bg-[#0d1730] border-2 border-dashed border-slate-500 flex items-center justify-center overflow-hidden shadow-lg shadow-black/30 group-hover:border-blue-500 transition">

                <!-- Placeholder icon (shown when no image selected yet) -->
                <svg id="placeholder" class="w-16 h-16 text-slate-500 group-hover:text-blue-400 transition" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4.5 20c1.5-4 4.5-6 7.5-6s6 2 7.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>

                <!-- Preview image (hidden until a file is chosen) -->
                <img id="imagePreview" src="" alt="Profile Preview" class="hidden w-full h-full object-cover">

                <!-- Hover overlay -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <span class="text-white text-[11px] font-semibold tracking-wide">CHANGE PHOTO</span>
                </div>
               
            </div>
        </label>

        <input
            type="file"
            name="profile_picture"
            id="profile_picture"
            accept="image/png, image/jpeg, image/jpg"
            class="hidden"
        
            onchange="previewImage(event)">

              </form>

        <p class="text-slate-400 text-[11px] mt-3 text-center max-w-[180px]">
            JPG or PNG. Max size 2MB.
        </p>

        <p id="profilePictureError" class="text-red-400 text-[11px] mt-1 text-center max-w-[180px] hidden"></p>
      </div>
      </div>

      
   <script>
function previewImage(event) {

    const file = event.target.files[0];
    const errorEl = document.getElementById('profilePictureError');

    errorEl.classList.add('hidden');
    errorEl.textContent = '';

    if (!file) return;

    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    const maxSizeBytes = 2 * 1024 * 1024; // 2MB

    if (!allowedTypes.includes(file.type)) {
        errorEl.textContent = 'Only JPG or PNG files are allowed.';
        errorEl.classList.remove('hidden');
        event.target.value = '';
        return;
    }

    if (file.size > maxSizeBytes) {
        errorEl.textContent = 'File must be 2MB or smaller.';
        errorEl.classList.remove('hidden');
        event.target.value = '';
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        document.getElementById('imagePreview').src = e.target.result;
        document.getElementById('imagePreview').classList.remove('hidden');
        document.getElementById('placeholder').classList.add('hidden');
    };

    reader.readAsDataURL(file);
}

/*
  Prevent name-type fields (First Name, Middle Name, Last Name, Nationality)
  from starting with a lowercase letter. Once there is already a character
  in the field, lowercase letters are allowed as normal (e.g. "McDonald",
  "dela Cruz" style middle characters still work).
*/
document.querySelectorAll('.name-field').forEach(function (input) {

    input.addEventListener('keydown', function (e) {

        // Only intercept plain single-letter keys (ignore Ctrl/Cmd combos, arrows, etc.)
        if (e.ctrlKey || e.metaKey || e.altKey) return;

        const isLowercaseLetter = /^[a-z]$/.test(e.key);

        if (!isLowercaseLetter) return;

        const atStart = input.selectionStart === 0 && input.selectionEnd === 0;
        const fieldIsEmpty = input.value.length === 0;

        // Block a lowercase letter only when it would become the very first character
        if (atStart && fieldIsEmpty) {
            e.preventDefault();
        }

    });

    input.addEventListener('paste', function (e) {

        const pasted = (e.clipboardData || window.clipboardData).getData('text');

        if (pasted.length === 0) return;

        const atStart = input.selectionStart === 0 && input.selectionEnd === 0;
        const fieldIsEmpty = input.value.length === 0;

        if (atStart && fieldIsEmpty && /^[a-z]/.test(pasted)) {
            e.preventDefault();

            // Auto-capitalize the first letter of the pasted text instead of rejecting it outright
            const fixed = pasted.charAt(0).toUpperCase() + pasted.slice(1);
            input.value = fixed;
        }

    });

});

/*
  Phone Number field:
  - digits only (no letters, symbols, spaces)
  - hard cap of 11 digits (e.g. 09171234567)
*/
const phoneInput = document.getElementById('phone');

phoneInput.addEventListener('keydown', function (e) {

    const allowedKeys = [
        "Backspace","Delete","Tab","Escape","Enter",
        "ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Home","End"
    ];

    if (allowedKeys.includes(e.key)) return;

    if (e.ctrlKey || e.metaKey) return;

    const isDigit = /^[0-9]$/.test(e.key);

    // Block non-digit keys entirely
    if (!isDigit) {
        e.preventDefault();
        return;
    }

    // Block extra digits once 11 digits are already entered
    // (unless there's a selection being replaced)
    const hasSelection = phoneInput.selectionStart !== phoneInput.selectionEnd;

    if (!hasSelection && phoneInput.value.length >= 11) {
        e.preventDefault();
    }

});

phoneInput.addEventListener('input', function () {

    // Backup sanitizer: strip non-digits and enforce 11-digit cap
    // (covers autofill, voice input, etc.)
    let cleaned = phoneInput.value.replace(/[^0-9]/g, '');

    if (cleaned.length > 11) {
        cleaned = cleaned.slice(0, 11);
    }

    if (phoneInput.value !== cleaned) {
        phoneInput.value = cleaned;
    }

});

phoneInput.addEventListener('paste', function (e) {

    e.preventDefault();

    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const digitsOnly = pasted.replace(/[^0-9]/g, '');

    const start = phoneInput.selectionStart;
    const end = phoneInput.selectionEnd;
    const current = phoneInput.value;

    let result = current.slice(0, start) + digitsOnly + current.slice(end);

    if (result.length > 11) {
        result = result.slice(0, 11);
    }

    phoneInput.value = result;

});

/*
  Email field:
  - standard max length of 254 characters (RFC 5321 practical limit),
    enforced both via the maxlength attribute and here as a backup
    in case maxlength is ever removed or bypassed.
*/
const emailInput = document.getElementById('email');
const EMAIL_MAX_LENGTH = 254;

emailInput.addEventListener('input', function () {

    if (emailInput.value.length > EMAIL_MAX_LENGTH) {
        emailInput.value = emailInput.value.slice(0, EMAIL_MAX_LENGTH);
    }

});

const firstNameInput = document.querySelector('input[name="first_name"]');
const lastNameInput = document.querySelector('input[name="last_name"]');
const companyEmailPreview = document.getElementById('company_email_preview');
const existingCompanyEmails = @json(\Modules\HR\Models\Employee::pluck('company_email')->filter()->values());

function buildCompanyEmail(firstName, lastName) {
    const first = (firstName || '').replace(/\s+/g, '').toLowerCase();
    const last = (lastName || '').replace(/\s+/g, '').toLowerCase();
    if (!first || !last) return '';

    const base = first + last;
    let email = base + '@nexora.com';
    if (!existingCompanyEmails.includes(email)) return email;

    let suffix = 2;
    while (existingCompanyEmails.includes(base + suffix + '@nexora.com')) {
        suffix++;
    }
    return base + suffix + '@nexora.com';
}

function updateCompanyEmailPreview() {
    if (!companyEmailPreview) return;
    companyEmailPreview.value = buildCompanyEmail(firstNameInput?.value, lastNameInput?.value);
}

firstNameInput?.addEventListener('input', updateCompanyEmailPreview);
lastNameInput?.addEventListener('input', updateCompanyEmailPreview);
updateCompanyEmailPreview();
</script>

</body>
</html>
