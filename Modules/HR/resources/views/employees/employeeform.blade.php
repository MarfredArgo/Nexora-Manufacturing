<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');

        * { font-family: "Inter", sans-serif; }

        /* Kept custom: things Tailwind utilities can't express cleanly */
        .id-card-front-bg {
            background: linear-gradient(180deg, #0B1E3D 0%, #0d4fde 180%);
        }
        .id-card-position-bg {
            background: linear-gradient(180deg, #0B1E3D 0%, #0D4FDE 180%);
        }
        .inside-select {
            text-align-last: center;
        }
        .inside-select option {
            background: #0B1E3D;
            color: #FFFFFF;
        }
        .inside-select option[value=""] { color: #8FA6D8; }
        .inside-select:invalid { color: #8FA6D8; }
        .inside-select:valid { color: #FFFFFF; }
    </style>
</head>

<body class="m-0 p-0 bg-[#18386d] text-white">

    <!-- =====================================================
         TOP NAVBAR
    ====================================================== -->
    @include('partials.navbar')

    <!-- =====================================================
         ID PREVIEW
    ====================================================== -->
    <div class="my-[30px] pt-10 pb-10 relative flex justify-center items-center">

        <a href="{{ route('hr.employees.index') }}" class="absolute top-[1px] left-[120px] inline-flex items-center gap-2 py-[9px] px-[30px] bg-[#0061FF20] text-white no-underline rounded-xl text-base font-normal shadow-[0_8px_20px_rgba(0,0,0,.25)] transition-all duration-250 hover:bg-[#0063FF10] hover:-translate-y-0.5 active:scale-[.97]">
            â† EMPLOYEE LIST
        </a>

        <div class="w-full max-w-[108.1875rem] min-h-[45.375rem] relative mt-5 flex justify-center items-center rounded-[18px] overflow-hidden bg-[#0B1E3D] pb-[90px]">

            <!-- BIG NEXORA BACKGROUND -->
            <img src="{{ asset('images/Nexora_Logo_Transparent(1).png') }}"
                 class="absolute w-[1325px] h-auto left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 rotate-[-5deg] opacity-[.0244] z-0 pointer-events-none"
                 alt="">

            <div class="flex justify-center items-center gap-[45px]" id="downloadArea">

                <!-- FRONT -->
                <div class="w-[268px] h-[452px] rounded-[1px] overflow-hidden relative id-card-front-bg text-white shadow-[0_15px_40px_rgba(0,0,0,.4),inset_0_0_0_1px_rgba(255,255,255,.05),inset_0_3px_0_rgba(255,255,255,.05)]">

                    <div class="absolute -left-[10%] w-[120%] h-[110px] bg-[#1B396B] rounded-bl-[60%] rounded-br-[60%] z-[1]"></div>

                    <!-- Background Logo -->
                    <img src="{{ asset('images/Nexora_Logo_Transparent(1).png') }}"
                         class="absolute w-[350px] h-auto left-1/2 top-[60%] -translate-x-1/2 -translate-y-1/2 rotate-[-8deg] opacity-[.03] pointer-events-none z-[1]"
                         alt="">

                    <div class="relative z-[5] flex justify-center pt-[5px]">
                        <img src="{{ asset('images/logo.png') }}" class="w-[268px] pb-[2px]" alt="Nexora Logo">
                    </div>

                    <div class="relative z-[5] w-[120px] h-[120px] mx-auto -mt-[5px] mb-[18px] rounded-full overflow-hidden border-[10px] border-[#0B1E3D] shadow-[0_10px_25px_rgba(0,0,0,.35)]">
                        @if($employee->profile_picture)
                            <img src="{{ asset('profile_pictures/'.$employee->profile_picture) }}"
                                 class="w-[110px] h-[110px] object-cover"
                                 alt="Employee Picture">
                        @else
                            <img src="{{ asset('images/avatar-placeholder.png') }}"
                                 class="w-[110px] h-[110px] object-cover bg-[lightblue]"
                                 alt="">
                        @endif
                    </div>

                    <div class="relative z-[5] flex flex-col items-center justify-center text-center pt-[10px] w-full mt-2 leading-[.90]">
                        <span id="idFirstName" class="text-[1.25rem] font-light uppercase">
                            {{ strtoupper(trim($employee->first_name )) }}
                        </span>
                        <strong id="idLastName" class="mt-[3px] text-[1.75rem] font-medium uppercase">
                            {{ strtoupper($employee->last_name) }}
                        </strong>
                    </div>

                    <div class="relative z-[5] w-[208px] h-[30px] py-[5px] px-[2px] text-[11.1px] mt-[10%] ml-[10.3%] flex flex-col items-center justify-center text-center rounded-[100px] id-card-position-bg shadow-[inset_1px_1px_2px_rgba(255,255,255,.45),3px_3px_8px_rgba(0,0,0,.22)]">
                        {{ strtoupper($employee->position) }}
                    </div>

                    <div class="relative z-[5] w-[165px] h-6 py-[5px] px-[2px] text-[0.8125rem] font-light mt-[10%] ml-[38%]">
                        {{ '2026' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                    </div>

                </div>

                <!-- BACK -->
                <div class="w-[268px] h-[452px] relative overflow-hidden id-card-front-bg text-white shadow-[0_15px_40px_rgba(0,0,0,.4),inset_0_0_0_1px_rgba(255,255,255,.05),inset_0_3px_0_rgba(255,255,255,.05)] px-[9px] pt-[15px] pb-[28%] text-center">

                    <!-- Background Logo -->
                    <img src="{{ asset('images/Nexora_Logo_Transparent(1).png') }}"
                         class="absolute w-[340px] h-auto left-1/2 top-[55%] -translate-x-1/2 -translate-y-1/2 rotate-[-8deg] opacity-[.03] pointer-events-none z-[1]"
                         alt="">

                    <h2 class="relative z-[5] text-[0.625rem]">COMPANY POLICY</h2>

                    <p class="relative z-[5] text-[0.6875rem] px-[5px] pt-3 pb-[20%] font-light tracking-[.1px]">
                        Property of Nexora. If found, please return to the Human
                        Resources Department. This card is non-transferable and
                        must be surrendered upon separation from the company.
                    </p>

                    <div class="relative z-[5] text-[0.8125rem] text-left px-[10px] pb-[30px] leading-[2.2]">
                        <p>
                            <span class="font-medium">Email:</span>
                            <span class="font-extralight">{{ $employee->email ?? 'N/A' }}</span>
                        </p>
                        <p>
                            <span class="font-medium">Phone:</span>
                            <span class="font-extralight">{{ $employee->phone ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div class="relative z-[5] flex justify-between gap-5 mt-[1px] px-5 py-10">
                        <!-- Authorized Signature -->
                        <div class="w-[48%] text-center">
                            <div class="text-[0.6875rem] font-light text-white mb-1.5 whitespace-nowrap overflow-hidden text-ellipsis">ADMIN</div>
                            <div class="w-full h-[0.2px] bg-white mb-1.5"></div>
                            <p class="text-[0.6875rem] tracking-[.5px] text-[#DCE8FF]">AUTHORIZED SIGNATURE</p>
                        </div>

                        <!-- Employee Signature -->
                        <div class="w-[48%] text-center">
                            <div class="text-[0.6875rem] font-light text-white mb-1.5 whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}
                            </div>
                            <div class="w-full h-[0.2px] bg-white mb-1.5"></div>
                            <p class="text-[0.6875rem] tracking-[.5px] text-[#DCE8FF]">EMPLOYEE SIGNATURE</p>
                        </div>
                    </div>

                   
                </div>

            </div>

            <!-- DOWNLOAD BUTTON -->
            <div class="absolute bottom-[25px] left-1/2 -translate-x-1/2 pb-10">
                <button type="button" id="downloadBtn"
                    class="w-[218px] h-[61px] border-0 border-[0.1px] border-[#dcdcdc54] rounded-md bg-[#0061FF20] text-white text-[0.9375rem] font-normal tracking-[.3px] cursor-pointer shadow-[0_8px_20px_rgba(0,0,0,.25)] transition-all duration-250 hover:bg-[#0061FF10] hover:-translate-y-0.5 active:scale-[.97]">
                    â†“ DOWNLOAD
                </button>
            </div>

        </div>
    </div>

    <!-- =====================================================
         EDIT FORM
    ====================================================== -->
    <div class="w-full max-w-[108.1875rem] min-h-[45.375rem] ml-[90px] mr-10 mt-5 py-7 pr-2.5 pl-[60px] grid grid-cols-[68%_32%] gap-6 bg-[#122A58] rounded-[22px] shadow-[inset_5px_10px_18px_rgba(191,0,0,.03),inset_1px_0_1px_rgba(0,0,0,.20),0_18px_35px_rgba(0,0,0,.35)]">

        <form action="{{ route('hr.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="contents">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-[70%_30%] gap-[30px] items-start col-span-2">

                <!-- LEFT SIDE -->
                <div class="pl-10">

                    <!-- Profile Image -->
                    <!-- Profile Image -->
<div>
    <h3 class="text-[13px] font-normal text-[#D7E4FF] tracking-[.5px] mb-[7px] uppercase">PROFILE IMAGE</h3>

    <label for="edit_profile_picture" class="cursor-pointer group inline-block">
        <div class="relative w-[100px] h-[100px] rounded-full bg-[#7FB3FF] flex justify-center items-center overflow-hidden shadow-[0_4px_12px_rgba(0,0,0,.35)]">

            <img id="editProfilePreview"
                 src="{{ $employee->profile_picture ? asset('profile_pictures/'.$employee->profile_picture) : '' }}"
                 alt="Profile"
                 class="w-full h-full object-cover rounded-full {{ $employee->profile_picture ? '' : 'hidden' }}">

            <i id="editProfilePlaceholder"
               class="fa-solid fa-circle-user text-[120px] text-[#1C4176] {{ $employee->profile_picture ? 'hidden' : '' }}"></i>

            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                <span class="text-white text-[9px] font-semibold tracking-wide text-center px-2">CHANGE<br>PHOTO</span>
            </div>
        </div>
    </label>

    <input type="file"
           name="profile_picture"
           id="edit_profile_picture"
           accept="image/png, image/jpeg, image/jpg"
           class="hidden"
           onchange="previewEditProfilePicture(event)">

    <p id="editProfilePictureError" class="text-red-400 text-[10px] mt-1.5 hidden"></p>
</div>

                    <div class="flex items-end gap-2 mb-3.5 mt-6">
                        <h3 class="text-[13px] font-light text-white uppercase whitespace-nowrap m-0">Employee Details</h3>

                        <div class="flex gap-3.5 ml-[146px] w-full">
                            <div class="relative w-[210.4px]">
                                <label class="absolute top-[3px] left-4 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Hire Date</label>
                                <input type="text" readonly value="{{ $employee->created_at->format('M d, Y') }}"
                                    class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none cursor-not-allowed">
                            </div>

                            <div class="relative w-[210.4px]">
                                <label class="absolute top-[3px] left-4 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Status</label>
                                <input type="text" readonly value="Active"
                                    class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- NAME ROW -->
<div class="flex gap-[15px] mb-[15px]">

    <div class="relative w-[220px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">First Name</label>
        <input name="first_name" id="first_name" value="{{ old('first_name',$employee->first_name) }}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <div class="relative w-[220px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Middle Name</label>
        <input name="middle_name" id="middle_name" value="{{ old('middle_name',$employee->middle_name) }}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <div class="relative w-[220px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Last Name</label>
        <input name="last_name" id="last_name" value="{{ old('last_name',$employee->last_name) }}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <div class="relative w-[120px]">
    <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Suffix</label>
    <select name="suffix" id="suffix"
        class="inside-select w-full h-10 box-border py-3 px-2.5 pt-3 bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">
        <option value="" {{ old('suffix', $employee->suffix) == null ? 'selected' : '' }}>None</option>
        <option value="Jr." {{ old('suffix', $employee->suffix) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
        <option value="Sr." {{ old('suffix', $employee->suffix) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
        <option value="II" {{ old('suffix', $employee->suffix) == 'II' ? 'selected' : '' }}>II</option>
        <option value="III" {{ old('suffix', $employee->suffix) == 'III' ? 'selected' : '' }}>III</option>
        <option value="IV" {{ old('suffix', $employee->suffix) == 'IV' ? 'selected' : '' }}>IV</option>
    </select>
</div>

</div>

                    <!-- Row 2: Department / Position -->
                    <div class="flex gap-[15px] mb-[15px]">

                        <div class="relative w-[406px]">
                            <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Department</label>
                            <select id="department" name="department" required
                                class="inside-select w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">
                                <option value="Business Intelligence" {{ $employee->department == 'Business Intelligence' ? 'selected' : '' }}>Business Intelligence</option>
                                <option value="E-commerce" {{ $employee->department == 'E-commerce' ? 'selected' : '' }}>E-commerce</option>
                                <option value="Finance" {{ $employee->department == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Human Resources" {{ $employee->department == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                <option value="IT Service Management" {{ $employee->department == 'IT Service Management' ? 'selected' : '' }}>IT Service Management</option>
                                <option value="Inventory Management" {{ $employee->department == 'Inventory Management' ? 'selected' : '' }}>Inventory Management</option>
                                <option value="Order Management" {{ $employee->department == 'Order Management' ? 'selected' : '' }}>Order Management</option>
                                <option value="Procurement Management" {{ $employee->department == 'Procurement Management' ? 'selected' : '' }}>Procurement Management</option>
                                <option value="Production Management" {{ $employee->department == 'Production Management' ? 'selected' : '' }}>Production Management</option>
                            </select>
                        </div>

                        <div class="relative w-[406px]">
                            <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Position</label>
                            <select id="position" name="position" required data-current="{{ $employee->position }}"
                                class="inside-select w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">
                                <!-- populated dynamically based on the selected department -->
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Gender / Marital Status -->
                <!-- Row 3: Gender / Marital Status / Nationality -->
<div class="flex gap-[15px] mb-[15px]">

    <div class="relative w-[269px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Gender</label>
        <select name="gender"
            class="inside-select w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">
            <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Prefer not to say" {{ $employee->gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
        </select>
    </div>

    <div class="relative w-[269px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Marital Status</label>
        <select name="marital_status"
            class="inside-select w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">
            <option value="Single" {{ $employee->marital_status == 'Single' ? 'selected' : '' }}>Single</option>
            <option value="Married" {{ $employee->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
            <option value="Widowed" {{ $employee->marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
            <option value="Separated" {{ $employee->marital_status == 'Separated' ? 'selected' : '' }}>Separated</option>
            <option value="Divorced" {{ $employee->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
        </select>
    </div>

    <div class="relative w-[269px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Nationality</label>
        <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $employee->nationality) }}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

</div>

                    <!-- Row 4: Address -->
                    <div class="mb-[15px]">
    <div class="relative w-[837px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Address</label>
        <textarea name="address" id="address" rows="1"
            class="w-full h-10 overflow-hidden box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none resize-none text-center flex items-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)]">{{ old('address', $employee->address) }}</textarea>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-[12px] w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>

                    <!-- Contact Details -->
                    <div class="mb-[15px]">
                        <h3 class="text-[13px] font-light text-white uppercase whitespace-nowrap mb-[15px] mt-[30px]">Contact Details</h3>

                        
    <div class="relative w-[837px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $employee->email) }}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>

                    <!-- Phone -->
                   <div class="mb-[15px]">
    <div class="relative w-[837px] pr-[430px]">
        <label class="absolute top-[3px] left-[16.5px] text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Phone Number</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
            maxlength="11" inputmode="numeric" pattern="\d{11}"
            class="w-full h-10 box-border py-3 px-2.5 pt-3 pl-[-38px] bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[11px] outline-none text-center focus:border-[#5D8CFF] focus:shadow-[0_0_0_2px_rgba(93,140,255,.2)] placeholder:text-[#8FA6D8]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="absolute left-[45%] top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white opacity-90">
            <path d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</div>

                </div>

                <!-- RIGHT SIDE -->
               <div class="flex flex-col gap-[15px] mt-[22%] w-[536px] -ml-[50%]">

                    <h3 class="text-[13px] font-light text-white uppercase whitespace-nowrap">Supporting Documents</h3>

                    @php
                        $docUrl = function (?string $path) {
                            return $path ? asset('storage/'.$path) : null;
                        };
                        $docName = function (?string $path) {
                            return $path ? basename($path) : null;
                        };
                    @endphp

                    <div class="relative w-[536px]">
                        <label class="absolute top-[3px] left-2.5 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">CV</label>
                        @if($employee->curriculum_vitae)
                            <div class="w-[536px] min-h-8 box-border py-2 px-2.5 pt-4 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[12px] flex items-center justify-between gap-2">
                                <span class="truncate pl-0.5">{{ $docName($employee->curriculum_vitae) }}</span>
                                <a href="{{ $docUrl($employee->curriculum_vitae) }}" target="_blank" rel="noopener"
                                   class="shrink-0 text-[#5D8CFF] hover:text-white text-[11px] no-underline">View</a>
                            </div>
                        @else
                            <input type="file" name="curriculum_vitae"
                                class="w-[536px] h-8 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[13px] cursor-pointer file:hidden">
                        @endif
                    </div>

                    <div class="relative w-[536px]">
                        <label class="absolute top-[3px] left-2.5 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Birth Certificate</label>
                        @if($employee->birth_certificate)
                            <div class="w-[536px] min-h-8 box-border py-2 px-2.5 pt-4 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[12px] flex items-center justify-between gap-2">
                                <span class="truncate pl-0.5">{{ $docName($employee->birth_certificate) }}</span>
                                <a href="{{ $docUrl($employee->birth_certificate) }}" target="_blank" rel="noopener"
                                   class="shrink-0 text-[#5D8CFF] hover:text-white text-[11px] no-underline">View</a>
                            </div>
                        @else
                            <input type="file" name="birth_certificate"
                                class="w-[536px] h-8 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[13px] cursor-pointer file:hidden">
                        @endif
                    </div>

                    <div class="relative w-[536px]">
                        <label class="absolute top-[3px] left-2.5 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Contract</label>
                        <input type="file" name="contract"
                            class="w-[536px] h-8 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[13px] cursor-pointer file:hidden">
                    </div>

                    <div class="relative w-[536px]">
                        <label class="absolute top-[3px] left-2.5 text-[9px] font-semibold text-[#6B7280] pointer-events-none z-[2]">Valid ID</label>
                        @if($employee->valid_id)
                            <div class="w-[536px] min-h-8 box-border py-2 px-2.5 pt-4 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[12px] flex items-center justify-between gap-2">
                                <span class="truncate pl-0.5">{{ $docName($employee->valid_id) }}</span>
                                <a href="{{ $docUrl($employee->valid_id) }}" target="_blank" rel="noopener"
                                   class="shrink-0 text-[#5D8CFF] hover:text-white text-[11px] no-underline">View</a>
                            </div>
                        @else
                            <input type="file" name="valid_id"
                                class="w-[536px] h-8 box-border py-3 px-2.5 pt-3 pr-[38px] bg-[#0B1E3D] text-[#8FA6D8] border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[13px] cursor-pointer file:hidden">
                        @endif
                    </div>

                </div>

            </div>

            <!-- ACTION BUTTONS -->
          <div class="col-span-2 flex justify-center items-center gap-[30px]">
                <button type="submit"
                    class="w-[218px] h-12 border border-[#5D8CFF] rounded-[10px] text-[15px] font-light cursor-pointer transition-all duration-250 shadow-[inset_0_2px_3px_rgba(61,49,49,.15),inset_0_8px_12px_rgba(255,255,255,.05)] bg-[#00FF0820] text-white hover:bg-[#00FF0850]">
                    SAVE
                </button>

                <button type="button" id="undoBtn"
                    class="w-[218px] h-12 border border-[#5D8CFF] rounded-[10px] text-[15px] font-light cursor-pointer transition-all duration-250 shadow-[inset_0_2px_3px_rgba(61,49,49,.15),inset_0_8px_12px_rgba(255,255,255,.05)] bg-[#0048FF20] text-white hover:bg-[#0048FF50]">
                    EDIT
                </button>

                </form>

               <form action="{{ route('hr.employees.destroy', $employee->id) }}" method="POST" id="deleteForm">
    @csrf
    @method('DELETE')

    @if(session('employee_role') === 'admin')
    <button type="submit"
        class="w-[218px] h-12 border border-[#5D8CFF] rounded-[10px] text-[15px] font-light cursor-pointer transition-all duration-250 shadow-[inset_0_2px_3px_rgba(61,49,49,.15),inset_0_8px_12px_rgba(255,255,255,.05)] bg-[#FF000420] text-white hover:bg-[#C0392B95]">
        DELETE
    </button>
    @endif
</form>

    </div>

    <!-- =====================================================
     DELETE CONFIRMATION MODAL
====================================================== -->
<div id="deleteModalOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[2000] hidden items-center justify-center">
    <div class="w-[420px] bg-[#132B52] rounded-[18px] shadow-[0_20px_45px_rgba(0,0,0,.45),inset_0_1px_0_rgba(255,255,255,.05)] p-7 relative border border-white/5">

        <!-- Icon -->
        <div class="w-14 h-14 rounded-full bg-[#FF000420] flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-[#FF6B6B]" viewBox="0 0 24 24" fill="none">
                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                      stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h2 class="text-white text-lg font-medium text-center mb-2">Delete Employee?</h2>

        <p class="text-[#C9DAF8] text-[13px] text-center leading-relaxed mb-1">
            Are you sure you want to delete
        </p>
        <p class="text-white text-[15px] font-semibold text-center mb-1">
            {{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}
        </p>
        <p class="text-[#8FA6D8] text-[11px] text-center mb-5">
            Employee ID: {{ '2026' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
        </p>

        <p class="text-[#8FA6D8] text-[11px] text-center mb-4 leading-relaxed">
            This action cannot be undone. Type <span class="text-[#FF6B6B] font-semibold tracking-wide">DELETE</span> below to confirm.
        </p>

        <input type="text" id="deleteConfirmInput" autocomplete="off"
            placeholder="Type DELETE to confirm"
            class="w-full h-11 box-border py-3 px-3.5 bg-[#0B1E3D] text-white border-0 shadow-[0_4px_8px_rgba(0,0,0,.35)] rounded-[10px] text-[13px] outline-none text-center tracking-[2px] uppercase placeholder:text-[#8FA6D8] placeholder:tracking-normal placeholder:normal-case focus:shadow-[0_0_0_2px_rgba(255,107,107,.35)] mb-5">

        <div class="flex gap-3">
            <button type="button" id="cancelDeleteBtn"
                class="flex-1 h-11 border border-[#5D8CFF] rounded-[10px] text-[13px] font-light text-white bg-[#0048FF20] hover:bg-[#0048FF50] transition-all duration-250 cursor-pointer">
                CANCEL
            </button>
            <button type="button" id="confirmDeleteBtn" disabled
                class="flex-1 h-11 border border-[#5D8CFF] rounded-[10px] text-[13px] font-light text-white bg-[#FF000420] opacity-40 cursor-not-allowed transition-all duration-250">
                DELETE
            </button>
        </div>
    </div>
</div>

    <script>
        // Positions available per department (kept in sync with the departments list above)
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

        function populatePositions(selectedDepartment, positionToSelect) {
            const positions = positionsByDepartment[selectedDepartment] || [];

            positionSelect.innerHTML = "";

            if (!positions.length) {
                positionSelect.appendChild(new Option("Select Department First", ""));
                return;
            }

            positions.forEach(function (position) {
                const option = new Option(position, position);
                if (position === positionToSelect) {
                    option.selected = true;
                }
                positionSelect.appendChild(option);
            });
        }

        // On load, populate positions for the employee's current department
        // and pre-select their current position.
        populatePositions(departmentSelect.value, positionSelect.dataset.current);

        // When the department changes, refresh the position list (no pre-selection).
        departmentSelect.addEventListener("change", function () {
            populatePositions(this.value, null);
        });
    </script>

    <script>
        document.getElementById("downloadBtn").addEventListener("click", async function () {

            const { jsPDF } = window.jspdf;
            const area = document.getElementById("downloadArea");

            const canvas = await html2canvas(area, {
                scale: 4,
                useCORS: true,
                backgroundColor: null
            });

            const imgData = canvas.toDataURL("image/png");

            const pdf = new jsPDF({
                orientation: "landscape",
                unit: "mm",
                format: "a4"
            });

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const imgWidth = pageWidth - 20;
            const imgHeight = canvas.height * imgWidth / canvas.width;

            const y = (pageHeight - imgHeight) / 2;

            pdf.addImage(imgData, "PNG", 10, y, imgWidth, imgHeight);
            pdf.save("Employee_ID.pdf");
        });

        function adjustNameSize() {
            const first = document.getElementById("idFirstName");
            const last = document.getElementById("idLastName");

            const firstLength = first.textContent.trim().length;
            const lastLength = last.textContent.trim().length;

            if (firstLength > 22) {
                first.style.fontSize = "1.15rem";
            } else if (firstLength > 16) {
                first.style.fontSize = "1.25rem";
            } else {
                first.style.fontSize = "1.25rem";
            }

            if (lastLength > 15) {
                last.style.fontSize = "1.75rem";
            } else {
                last.style.fontSize = "1.75rem";
            }
        }

        adjustNameSize();

        document.getElementById("undoBtn").addEventListener("click", function () {
            if (confirm("Discard all unsaved changes?")) {
                location.reload();
            }
        });

        const deleteForm      = document.getElementById("deleteForm");
    const deleteModal     = document.getElementById("deleteModalOverlay");
    const deleteInput     = document.getElementById("deleteConfirmInput");
    const confirmDeleteBtn= document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    if (deleteForm) {
        // Intercept the normal submit and open the modal instead
        deleteForm.addEventListener("submit", function (e) {
            e.preventDefault();
            openDeleteModal();
        });
    }

    function openDeleteModal() {
        deleteInput.value = "";
        toggleConfirmBtn();
        deleteModal.classList.remove("hidden");
        deleteModal.classList.add("flex");
        setTimeout(() => deleteInput.focus(), 50);
    }

    function closeDeleteModal() {
        deleteModal.classList.add("hidden");
        deleteModal.classList.remove("flex");
    }

    function toggleConfirmBtn() {
        const isMatch = deleteInput.value.trim() === "DELETE";
        confirmDeleteBtn.disabled = !isMatch;
        confirmDeleteBtn.classList.toggle("opacity-40", !isMatch);
        confirmDeleteBtn.classList.toggle("cursor-not-allowed", !isMatch);
        confirmDeleteBtn.classList.toggle("bg-[#C0392B95]", isMatch);
        confirmDeleteBtn.classList.toggle("cursor-pointer", isMatch);
    }

    deleteInput.addEventListener("input", function () {
        // force caps so it's clearly "DELETE" while typing
        this.value = this.value.toUpperCase();
        toggleConfirmBtn();
    });

    // Allow Enter key to confirm once valid
    deleteInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && !confirmDeleteBtn.disabled) {
            deleteForm.submit();
        }
    });

    cancelDeleteBtn.addEventListener("click", closeDeleteModal);

    // Click outside modal card to cancel
    deleteModal.addEventListener("click", function (e) {
        if (e.target === deleteModal) closeDeleteModal();
    });

    confirmDeleteBtn.addEventListener("click", function () {
        if (deleteInput.value.trim() === "DELETE") {
            deleteForm.submit();
        }
    });

    /* =========================================================
       PHONE NUMBER: digits only, max 11
    ========================================================= */
    const phoneInput = document.getElementById("phone");
    phoneInput.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 11);
    });
    phoneInput.addEventListener("keypress", function (e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
    phoneInput.addEventListener("paste", function (e) {
        const pasted = (e.clipboardData || window.clipboardData).getData("text");
        if (!/^\d+$/.test(pasted)) {
            e.preventDefault();
        }
    });

    /* =========================================================
       BLOCK STARTING WITH LOWERCASE LETTER
       (First Name, Middle Name, Last Name, Address)
    ========================================================= */
    function blockLeadingLowercase(el) {
        el.addEventListener("input", function () {
            const val = this.value;
            if (val.length > 0 && /^[a-z]/.test(val)) {
                // auto-capitalize the first letter instead of just blocking
                this.value = val.charAt(0).toUpperCase() + val.slice(1);
            }
        });
    }

    ["first_name", "middle_name", "last_name", "address"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) blockLeadingLowercase(el);
    });

    function previewEditProfilePicture(event) {

    const file = event.target.files[0];
    const errorEl = document.getElementById('editProfilePictureError');
    const previewImg = document.getElementById('editProfilePreview');
    const placeholderIcon = document.getElementById('editProfilePlaceholder');

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
        previewImg.src = e.target.result;
        previewImg.classList.remove('hidden');
        placeholderIcon.classList.add('hidden');
    };

    reader.readAsDataURL(file);
}
    </script>

    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</body>


</html>
