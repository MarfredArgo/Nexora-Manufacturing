<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            background: #0B1E3D;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /*==========================
            SPLASH SCREEN 
        ===========================*/
        #splash {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            z-index: 99999;
            transition: opacity .6s ease;
        }

        .circle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #0B1E3D;
            border-radius: 50%;
            animation: spread .5s ease-out forwards;
        }

        @keyframes spread {
            0% { transform: scale(0); }
            100% { transform: scale(350); }
        }

        .brand {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .logo {
            width: 132px;
            height: 132px;
            opacity: 0;
            transform: scale(0) rotate(0deg);
            animation: logoIntro 0.5s ease forwards 0.8s, logoMove .8s ease forwards 2s;
        }

        @keyframes logoIntro {
            0% { opacity: 0; transform: scale(0) rotate(0deg); }
            100% { opacity: 1; transform: scale(1) rotate(360deg); }
        }

        @keyframes logoMove {
            from { transform: translateX(0); }
            to { transform: translateX(-170px); }
        }

        .banner {
            position: absolute;
            margin-left: 175px;
            width: 0;
            opacity: 0;
            transform: translateX(-80px);
            animation: bannerReveal .8s ease forwards 2.25s;
        }

        @keyframes bannerReveal {
            0% { width: 0; opacity: 0; transform: translateX(-150px); }
            100% { width: 420px; opacity: 1; transform: translateX(10px); }
        }

        /*==========================
            MAIN CONTENT LAYOUT
        ===========================*/
        .main-wrapper {
            opacity: 0;
            animation: showPage .8s ease forwards 4.1s;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Applied via JS when we need to skip the splash/intro (e.g. after a failed login) */
        .main-wrapper.no-intro {
            opacity: 1;
            animation: none;
        }

        @keyframes showPage {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Updated Header */
        .header {
            height: 128px;
            background: #0B1E3D;
            display: flex;
            align-items: flex-start; 
            z-index: 100;
        }

        /* Interactive Logo Formatting */
        .nexora-logo {
            display: block;
            margin: 16px 0 16px 16px; /* 16px on top, bottom, left */
            height: 96px; /* 128px - 16px top - 16px bottom */
            z-index: 999;
            transition: .3s ease;
        }

        .nexora-logo:hover {
            transform: scale(1.05);
        }

        .nexora-logo img {
            height: 100%;
            object-fit: contain;
            transition: .3s ease;
        }

        .nexora-logo:hover img {
            filter: drop-shadow(0 8px 20px rgba(0,0,0,.25));
        }

        /* Page Background */
        .page {
            flex: 1;
            display: flex;
            background-image: url("images/Banner Transparent.png");
            background-size: 1920px;
            background-repeat: no-repeat;
            background-position-x: bottom center;
            background-size: cover;
            
        }

        .form-col {
            flex: 0 0 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /*==========================
            LOGIN CARD STYLING
        ===========================*/
        .login-card {
            width: 500px;
            background: #0B1E3D;
            margin-top: -112px;
            margin-left: -128px;
            padding: 64px;
            border-radius: 8px;
            border: 1px solid #0B1E3D;
        }

        .login-card h1 {
            font-size: 24px;
            font-weight: 800;
            color: #D9DFE9;
            margin-bottom: 32px;
        }

        /* Error / warning banner shown when credentials are wrong */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(220, 38, 38, 0.12);
            border: 1px solid rgba(248, 113, 113, 0.5);
            color: #FCA5A5;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            padding: 12px 14px;
            border-radius: 4px;
            margin-bottom: 24px;
        }

        .alert-error svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 1px;
            stroke: #FCA5A5;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #0B1E3D;
        }

        input {
            width: 100%;
            height: 46px;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 0 16px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            color: #0B1E3D;
            background: #ffffff;
            transition: .2s;
        }

        input:focus {
            border: 1px solid #1B6FC8;
            box-shadow: 0 0 0 2px rgba(27, 111, 200, 0.2);
        }

        input.input-error {
            border: 1px solid #DC2626;
        }

        input::placeholder {
            color: #0B1E3D;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            color: #0B1E3D;
            opacity: 0.65;
            transition: opacity .2s;
            user-select: none;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        button {
            width: 100%;
            height: 48px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            background: #0D3662;
            color: white;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: .2s;
        }

        button:hover {
            background: #134e8e;
        }

        .links {
            margin-top: 32px;
            text-align: center;
        }

        .links p {
            font-size: 12px;
            color: #D9DFE9;
            margin-bottom: 8px;
        }

        .links a {
            color: #D9DFE9;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-weight: 600;
        }

        .links a:hover {
            color: #1B6FC8;
        }
    </style>
</head>

<body>

    <div id="splash">
        <div class="circle"></div>
        <div class="brand">
            <img src="images/Nexora_Logo_Transparent (2).png" class="logo" alt="Logo">
            <img src="images/banner2.png" class="banner" alt="Banner">
        </div>
    </div>

    <div class="main-wrapper" id="mainWrapper">
        
        <header class="header">
            <a href="{{ route('login') }}" class="nexora-logo">
                <img src="images/logo.png" alt="Nexora Logo">
            </a>
        </header>
        
        <main class="page">
            <div class="form-col">
                <div class="login-card">
                    <h1>Sign In</h1>

                    @if ($errors->any() || session('error'))
                        <div class="alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="13"></line>
                                <line x1="12" y1="16" x2="12" y2="16.01"></line>
                            </svg>
                            <span>{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ url('/login') }}">
    @csrf

  

    <div class="input-group">
        <label>Username / Email</label>
       <input
    type="email"
    name="username"
    placeholder="Enter Username or Email"
    value="{{ old('username') }}"
    class="{{ ($errors->any() || session('error')) ? 'input-error' : '' }}"
    required>
    </div>

    <div class="input-group">
        <label>Password</label>
        <div class="password-wrapper">
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Enter Password"
                class="{{ ($errors->any() || session('error')) ? 'input-error' : '' }}"
                required>
            <span class="toggle-password" id="togglePassword" role="button" aria-label="Show password" tabindex="0">
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a18.5 18.5 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 7 11 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </span>
        </div>
    </div>

    <button type="submit">Log In</button>
</form>
                    
                    <div class="links">
                        <p>Forgot Password? <a href="#">Reset</a></p>
                        <!--<p>Not registered yet? <a href="contactus.html" id="contactBtn">Contact Us</a></p>-->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const SPLASH_DURATION = 4300;
    const splash = document.getElementById("splash");
    const mainWrapper = document.getElementById("mainWrapper");
    const hasError = document.querySelector(".alert-error") !== null;

    if (hasError) {
        // A failed login just reloaded this page (e.g. server redirected back
        // with validation errors) â€” skip the splash intro entirely and show
        // the form immediately with the warning message already in place.
        splash.style.display = "none";
        mainWrapper.classList.add("no-intro");
    } else {
        // 1. Hide splash screen after initial load
        setTimeout(() => {
            splash.style.opacity = "0";
            splash.style.pointerEvents = "none";
        }, SPLASH_DURATION);
    }

    // 2. Smooth, fast fade-out for exiting the page
    function smoothExit(e, url) {
        e.preventDefault(); 
        
        // Create a quick white fade overlay
        const fader = document.createElement('div');
        fader.style.position = 'fixed';
        fader.style.inset = '0';
        fader.style.background = 'white';
        fader.style.opacity = '0';
        fader.style.transition = 'opacity 0.4s ease';
        fader.style.zIndex = '999999';
        document.body.appendChild(fader);

        // Trigger browser reflow to ensure the transition plays
        void fader.offsetWidth;
        fader.style.opacity = '1';

        // Redirect quickly after the screen goes white
        setTimeout(() => {
            window.location.href = url;
        }, 400); 
    }

    // Attach the new smooth exit to your links
    // (Note: This checks if the buttons exist first, so it works safely on both pages)
    
    const signInBtn = document.getElementById("signInBtn");
    const contactBtn = document.getElementById("contactBtn");
    const headerLogoBtn = document.getElementById("headerLogoBtn");

    if (signInBtn) signInBtn.addEventListener("click", (e) => smoothExit(e, "signIn.html"));
    if (contactBtn) contactBtn.addEventListener("click", (e) => smoothExit(e, "contactus.html"));
    if (headerLogoBtn) headerLogoBtn.addEventListener("click", (e) => smoothExit(e, "signIn.html"));

    // 3. Show / hide password toggle
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    function togglePasswordVisibility() {
        const isHidden = passwordInput.type === "password";

        passwordInput.type = isHidden ? "text" : "password";

        eyeOpen.style.display = isHidden ? "none" : "block";
        eyeClosed.style.display = isHidden ? "block" : "none";

        togglePassword.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
    }

    togglePassword.addEventListener("click", togglePasswordVisibility);

    togglePassword.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            togglePasswordVisibility();
        }
    });
</script>

</body>
</html>
