<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora - New User Setup</title>
    <link rel="stylesheet" href="style.css"> <!-- Replace with your actual asset path if needed -->
</head>

<style>
    /* Reset and Core Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Header Section */
    .header {
        height: 128px;
        background: #0B1E3D;
        display: flex;
        align-items: flex-start;
        z-index: 100;
    }

    .nexora-logo img {
        display: block;
        margin: 16px 0 16px 16px;
        height: 96px;
        z-index: 999;
        transition: .3s ease;
    }

    /* Page Background */
    .page {
        flex: 1;
        display: flex;
        background-image: url("{{ asset('images/newuser_bg.png') }}");
        background-size: 1920px;
        background-repeat: no-repeat;
        background-position: center -192px;
    }

    /* Layout Alignment Container */
    .container {
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        padding: 60px 80px;
    }

    /* Left Column Styling */
    .left-section {
        flex: 1;
        margin-top: -30px;
    }

    .welcome-text {
        font-size: 3.8rem;
        font-weight: 800;
        color: #0B1E3D;
        margin-bottom: 10px;
    }

    .date-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    /* Right Column Styling */
    .right-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-end; /* Keeps the container docked to the right */
    }

    /* Stepper Progress Bar */
    .stepper {
        display: flex;
        align-items: center;
        margin-bottom: 50px;
    }

    .step {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: #C4C4C4;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #5A5A5A;
    }

    .step.active {
        background-color: #A9DFBF;
        color: #196F3D;
    }

    .step svg {
        fill: #000;
        width: 22px;
        height: 22px;
        display: block;
        opacity: 0.5;
    }

    .step.active svg {
        fill: currentColor;
        stroke: currentColor;
        opacity: 1;
    }

    .line {
        width: 90px;
        height: 3px;
        background-color: #C4C4C4;
        margin: 0 5px;
    }

    .line.active {
        background-color: #A9DFBF;
    }

    /* Shared Form Styling */
    .form-container {
        width: 100%;
        max-width: 480px; /* Aligns with stepper width */
    }

    .form-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 30px;
        text-align: center;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 8px;
    }

    .input-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #A6ACAF;
        background-color: #E5E8E8;
        border-radius: 4px;
        font-size: 1rem;
        color: #555;
        font-style: italic;
    }

    .input-group input:focus {
        outline: none;
        border-color: #0B1E3D;
    }

    /* Button Layout */
    .button-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
        margin-bottom: 40px;
    }

    .submit-btn {
        background-color: #1B365D;
        color: white;
        padding: 12px 45px;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        transition: background-color 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .submit-btn:hover {
        background-color: #0B1E3D;
    }

    /* Password Requirements */
    .requirements-container h3 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 8px;
    }

    .requirements-list {
        list-style-type: none;
    }

    .requirements-list li {
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        position: relative;
        padding-left: 15px;
    }

    .requirements-list li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #000;
    }

    /* Upload step extras (Perfect Centering Applied Here) */
    .upload-wrapper {
        width: 100%;
        max-width: 320px;
        display: flex;
        flex-direction: column;
        margin: 0 auto; /* Centers the 320px wrapper inside the 480px form container */
        align-items: center;
    }

    .upload-zone {
        width: 320px;
        height: 320px;
        background-color: #D6DBDF;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        border: 2px dashed transparent;
        padding: 20px;
        text-align: center;
        box-sizing: border-box;
    }

    .upload-zone:hover {
        background-color: #CACFD2;
    }

    .upload-text-main {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2C3E50;
        margin-bottom: 6px;
    }

    .upload-text-or {
        font-size: 0.9rem;
        color: #566573;
        margin-bottom: 10px;
    }

    .upload-btn-text {
        font-size: 0.95rem;
        font-weight: 700;
        color: #000;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px 8px;
        transition: opacity 0.2s ease;
    }

    .upload-btn-text:hover {
        opacity: 0.7;
    }

    .upload-limits {
        font-size: 0.75rem;
        font-weight: 600;
        color: #7F8C8D;
        margin-top: 12px;
        text-align: center;
        width: 100%;
    }

    .upload-wrapper .button-container {
        width: 100%; /* Spans the full width of the 320px container */
        justify-content: flex-end; /* Aligns submit button to the right of the upload box */
    }

    /* Stage 4 complete adjustments */
    .form-container.stage4 {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .form-text {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        font-weight: 400;
        color: #000;
        margin-top: 15px;
        margin-bottom: 15px;
        line-height: 1.6;
        width: 100%;
    }

    .button-container.stage4 {
        width: 100%;
        justify-content: center; /* Center the final 'Done' button */
        margin-top: 30px;
    }
</style>

@php
    $stage = $stage ?? request()->query('stage', '1');
    if (!in_array((string)$stage, ['1','2','3','4'], true)) {
        $stage = '1';
    }
@endphp

<body>
    <header class="header">
        <a href="{{ route('login') }}" class="nexora-logo" id="headerLogoBtn">
            <img src="{{ asset('images/Banner Transparent.png') }}" alt="Nexora Logo">
        </a>
    </header>

    <div class="page">
        <div class="container">
            <div class="left-section">
                <h1 class="welcome-text">Welcome, {{ $company->company_name ?? 'Nexora Client' }}!</h1>
                <p class="date-text">{{ now()->format('l | F d, Y') }} | {{ $company->company_name ?? 'Nexora Client' }}</p>
            </div>

            <div class="right-section">
                <!-- Progress Stepper -->
                <div class="stepper">
                    @php
                        $s = (int)$stage;
                        $is1 = $s >= 1;
                        $is2 = $s >= 2;
                        $is3 = $s >= 3;
                        $is4 = $s >= 4;
                    @endphp

                    <div class="step {{ $is1 ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.5 11c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 1.5c-2.7 0-8 1.3-8 4v2h11v-2c0-2.7-5.3-4-8-4z" />
                            <path d="M18 10.5c-1.4 0-2.5 1.1-2.5 2.5 0 1 .6 1.9 1.5 2.3v3.7h2v-1h1v-1h-1v-1.7c.9-.4 1.5-1.3 1.5-2.3 0-1.4-1.1-2.5-2.5-2.5zm0 2.5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1z" />
                        </svg>
                    </div>

                    <div class="line {{ $is2 ? 'active' : '' }}"></div>

                    <div class="step {{ $is2 ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 3H4C2.9 3 2 3.9 2 5v10c0 1.1.9 2 2 2h9v-2H4V5h10v6h2V5c0-1.1-.9-2-2-2z" />
                            <circle cx="6.5" cy="7.5" r="1.5" />
                            <path d="M5 14l2.5-3 2 2.5 1.5-1.5 2 2H5z" />
                            <path d="M19.5 13.5c-.3 0-.5.2-.5.5v.6c-.3.1-.6.3-.9.5l-.5-.5c-.2-.2-.5-.2-.7 0l-.7.7c-.2.2-.2.5 0 .7l.5.5c-.2.3-.4.6-.5.9h-.6c-.3 0-.5.2-.5.5v1c0 .3.2.5.5.5h.6c.1.3.3.6.5.9l-.5.5c-.2.2-.2.5 0 .7l.7.7c.2.2.5.2.7 0l.5-.5c.3.2.6.4.9.5v.6c0 .3.2.5.5.5h1c.3 0 .5-.2.5-.5v-.6c.3-.1.6-.3.9-.5l.5.5c.2.2.5.2.7 0l.7-.7c.2-.2.2-.5 0-.7l-.5-.5c.2-.3.4-.6.5-.9h.6c.3 0 .5-.2.5-.5v-1c0-.3-.2-.5-.5-.5h-.6c-.1-.3-.3-.6-.5-.9l.5-.5c.2-.2.2-.5 0-.7l-.7-.7c-.2-.2-.5-.2-.7 0l-.5.5c-.3-.2-.6-.4-.9-.5v-.6c0-.3-.2-.5-.5-.5h-1zm.5 3.5a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </svg>
                    </div>

                    <div class="line {{ $is3 ? 'active' : '' }}"></div>

                    <div class="step {{ $is3 ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.5 11c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 1.5c-2.7 0-8 1.3-8 4v2h11v-2c0-2.7-5.3-4-8-4z" />
                            <path d="M19.5 13.5c-.3 0-.5.2-.5.5v.6c-.3.1-.6.3-.9.5l-.5-.5c-.2-.2-.5-.2-.7 0l-.7.7c-.2.2-.2.5 0 .7l.5.5c-.2.3-.4.6-.5.9h-.6c-.3 0-.5.2-.5.5v1c0 .3.2.5.5.5h.6c.1.3.3.6.5.9l-.5.5c-.2.2-.2.5 0 .7l.7.7c.2.2.5.2.7 0l.5-.5c.3.2.6.4.9.5v.6c0 .3.2.5.5.5h1c.3 0 .5-.2.5-.5v-.6c.3-.1.6-.3.9-.5l.5.5c.2.2.5.2.7 0l.7-.7c.2-.2.2-.5 0-.7l-.5-.5c.2-.3.4-.6.5-.9h.6c.3 0 .5-.2.5-.5v-1c0-.3-.2-.5-.5-.5h-.6c-.1-.3-.3-.6-.5-.9l.5-.5c.2-.2.2-.5 0-.7l-.7-.7c-.2-.2-.5-.2-.7 0l-.5.5c-.3-.2-.6-.4-.9-.5v-.6c0-.3-.2-.5-.5-.5h-1zm.5 3.5a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </svg>
                    </div>

                    <div class="line {{ $is4 ? 'active' : '' }}"></div>

                    <div class="step {{ $is4 ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- Stage Bodies -->
                @if ($stage === '1')
                    {{-- Stage 1: Set New Password --}}
                    <div class="form-container">
                        <h2 class="form-title">Set system admin password</h2>

                        @if ($errors->any())
                            <div style="margin-bottom: 16px; color: #B91C1C; font-weight: 700;">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('newuser.password') }}" method="POST">
                            @csrf

                            <div class="input-group">
                                <label for="new_password">New password</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                            </div>

                            <div class="input-group">
                                <label for="confirm_password">Re-enter password</label>
                                <input type="password" id="confirm_password" name="new_password_confirmation" placeholder="Re-enter new password" required>
                            </div>

                            <div class="button-container">
                                <button type="submit" class="submit-btn">Submit</button>
                            </div>
                        </form>

                        <div class="requirements-container">
                            <h3>Password Requirements</h3>
                            <ul class="requirements-list">
                                <li>At least 8 characters</li>
                                <li>At least one uppercase letter (A-Z)</li>
                                <li>At least one lowercase letter (a-z)</li>
                                <li>At least one number (0-9)</li>
                                <li>At least one special character (e.g., !, @, #, $, %)</li>
                                <li>Must not contain spaces</li>
                            </ul>
                        </div>
                    </div>
                @elseif ($stage === '2')
                    {{-- Stage 2: Upload Logo --}}
                    <div class="form-container">
                        <h2 class="form-title">Upload your company logo</h2>

                        @if ($errors->any())
                            <div style="margin-bottom: 16px; color: #B91C1C; font-weight: 700;">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('newuser.logo') }}" method="POST" id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-wrapper">
                                <input type="file" id="logo_input" name="logo" style="display: none;" accept="image/png, image/jpeg, image/jpg">

                                <div class="upload-zone" onclick="document.getElementById('logo_input').click()">
                                    <p class="upload-text-main">Drag & Drop your logo here</p>
                                    <p class="upload-text-or">or</p>
                                    <button type="button" class="upload-btn-text">[ Choose Image ]</button>
                                </div>

                                <p class="upload-limits">format: JPG/PNG, limit: 500x500</p>

                                <div class="button-container">
                                    <button type="submit" class="submit-btn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <script>
                        const uploadZone = document.querySelector('.upload-zone');
                        const fileInput = document.getElementById('logo_input');

                        fileInput.addEventListener('change', handleFileSelect);

                        uploadZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            uploadZone.style.backgroundColor = '#CACFD2';
                        });

                        uploadZone.addEventListener('dragleave', () => {
                            uploadZone.style.backgroundColor = '#D6DBDF';
                        });

                        uploadZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            uploadZone.style.backgroundColor = '#D6DBDF';

                            if (e.dataTransfer.files.length) {
                                fileInput.files = e.dataTransfer.files;
                                handleFileSelect();
                            }
                        });

                        function handleFileSelect() {
                            if (fileInput.files.length) {
                                const file = fileInput.files[0];
                                if (file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        uploadZone.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px;" />`;
                                    };
                                    reader.readAsDataURL(file);
                                } else {
                                    uploadZone.innerHTML = `
                                        <p class="upload-text-main" style="color: #C0392B;">Invalid File Type</p>
                                        <p class="upload-text-or">Please select an image</p>
                                        <button type="button" class="upload-btn-text">[ Choose Image ]</button>
                                    `;
                                }
                            }
                        }
                    </script>
                @elseif ($stage === '3')
                    {{-- Stage 3: Add HR Manager --}}
                    <div class="form-container">
                        <h2 class="form-title">Add HR manager</h2>

                        @if ($errors->any())
                            <div style="margin-bottom: 16px; color: #B91C1C; font-weight: 700;">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('newuser.hr-manager') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Type here..." required>
                            </div>

                            <div class="input-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Type here..." required>
                            </div>

                            <div class="input-group">
                                <label for="email">Personal Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Type here..." required>
                            </div>

                            <div class="input-group">
                                <label for="employee_id">Employee ID</label>
                                <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" placeholder="Type here..." required>
                            </div>

                            <div class="button-container">
                                <button type="submit" class="submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Stage 4: Setup Complete --}}
                    <div class="form-container stage4">
                        <h2 class="form-title">Setup Complete!</h2>

                        <p class="form-text">Your ERP workspace has been successfully configured.</p>
                        <p class="form-text">Your organization is now ready to start managing employees and business operations.</p>

                        <p class="form-text">The HR manager profile is now awaiting approval in the client ITSM dashboard's Pending Approvals tab. No HR login credentials have been generated. Credentials are created only when the client system admin approves the request.</p>

                        <div class="button-container stage4">
                            <a href="{{ route('login') }}" class="submit-btn" role="button" aria-label="Done">Done</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
