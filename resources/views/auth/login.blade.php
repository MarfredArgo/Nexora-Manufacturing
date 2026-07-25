<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Sign In</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/nexora-icon.png') }}">
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
            background: #ffffff;
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
            background-image: url("{{ asset('images/bg.png') }}");
            background-size: 1920px;
            background-repeat: no-repeat;
            background-position-x: bottom center;
            background-position-y: -192px;
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
            background: #F0F4F8;
            margin-top: -112px;
            margin-left: -128px;
            padding: 64px;
            border-radius: 8px;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .login-card h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0B1E3D;
            margin-bottom: 32px;
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

        input::placeholder {
            color: #A0A0A0;
        }

        button {
            width: 100%;
            height: 48px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            background: #0B1E3D;
            color: white;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: .2s;
        }

        button:hover {
            background: #132B52;
        }

        .links {
            margin-top: 32px;
            text-align: center;
        }

        .links p {
            font-size: 12px;
            color: #5B7A9D;
            margin-bottom: 8px;
        }

        .links a {
            color: #0B1E3D;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-weight: 600;
            cursor: pointer;
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
            <img src="{{ asset('images/Nexora_Logo_Transparent.png') }}" class="logo" alt="Logo">
            <img src="{{ asset('images/Banner Name White.png') }}" class="banner" alt="Banner">
        </div>
    </div>

    <div class="main-wrapper">
        
        <header class="header">
            <a href="{{ route('login') }}" class="nexora-logo" id="headerLogoBtn">
                <img src="{{ asset('images/Banner Transparent.png') }}" alt="Nexora Logo">
            </a>
        </header>
        
        <main class="page">
            <div class="form-col">
                <div class="login-card">
                    <h1>Sign In</h1>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="input-group">
                            <label for="username">Username/Email</label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Enter Username or Email" required>
                            @error('username')
                                <p style="color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" placeholder="Enter Password" required>
                        </div>
                        
                        <button type="submit">Log In</button>
                    </form>
                    
                    <div class="links">
                        <p>Forgot Password? <a href="{{ route('password.request') }}">Reset</a></p>
                        <p>Not registered yet? <a href="/contact" id="contactBtn">Contact Us</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const SPLASH_DURATION = 4300;
    const splash = document.getElementById("splash");

    // 1. Hide splash screen after initial load
    setTimeout(() => {
        splash.style.opacity = "0";
        splash.style.pointerEvents = "none";
    }, SPLASH_DURATION);

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
    const signInBtn = document.getElementById("signInBtn");
    const contactBtn = document.getElementById("contactBtn");
    const headerLogoBtn = document.getElementById("headerLogoBtn");

    if (signInBtn) signInBtn.addEventListener("click", (e) => smoothExit(e, "{{ route('login') }}"));
    if (contactBtn) contactBtn.addEventListener("click", (e) => smoothExit(e, "/contact"));
    if (headerLogoBtn) headerLogoBtn.addEventListener("click", (e) => smoothExit(e, "{{ route('login') }}"));
</script>

</body>
</html>
