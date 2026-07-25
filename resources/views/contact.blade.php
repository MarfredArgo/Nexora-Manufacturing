<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            /* Allows natural vertical scrolling, prevents horizontal overflow */
            overflow-x: hidden; 
            min-height: 100vh;
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
            MAIN PAGE LAYOUT
        ===========================*/
        .main-wrapper {
            opacity: 0;
            animation: showPage .8s ease forwards 4.1s;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @keyframes showPage {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* FIXED 128PX HEADER */
        .header {
            height: 128px;
            background: #0B1E3D;
            display: flex;
            align-items: center;
            justify-content: space-between; 
            z-index: 100;
            width: 100%;
        }

        /* LEFT LOGO */
        .nexora-logo {
            display: block;
            margin: 16px 0 16px 16px; 

            height: 96px; 
            transition: .3s ease;
        }

        .nexora-logo:hover {
            transform: scale(1.02);
        }

        .nexora-logo img {
            height: 100%;
            object-fit: contain;
            transition: .3s ease;
        }

        .nexora-logo:hover img {
            filter: drop-shadow(0 8px 20px rgba(0,0,0,.25));
        }

        /* HEADER CONTACT BUTTON */
        .header-contact-btn {
            margin-right: 32px;
            padding: 12px 24px;
            background: #ffffff;
            color: #0B1E3D;
            border: 2px solid #ffffff;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 800;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            transition: .2s;
            cursor: pointer;
        }

        .header-contact-btn:hover {
            background: #E2E8F0;
            border-color: #E2E8F0;
        }

        /* IMAGE CONTAINER & LAYOUT */
        .page-container {
            position: relative;
            width: 100%;
            display: block;
            margin-top: -18vh;
        }

        /* Displays image seamlessly, allowing natural vertical scroll */
        .content-img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* REQUEST A DEMO BUTTON - Positioned absolutely over the image */
        .demo-btn {
            position: absolute;
            top: 89%; /* Centers button vertically under the achievement box */
            left: 74%; /* Centers button horizontally under the achievement box */
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 260px;
            height: 56px;
            background: #ffffff;
            color: #0B1E3D;
            font-size: 18px;
            font-weight: 800;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 30px; 
            cursor: pointer;
            transition: .2s;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 10;
        }

        .demo-btn:hover {
            background: #E2E8F0;
            transform: translate(-50%, -53%);
            box-shadow: 0 12px 20px rgba(0,0,0,0.3);
        }

    </style>
</head>

<body>

    <!-- Splash Screen -->
    <div id="splash">
        <div class="circle"></div>
        <div class="brand">
            <img src="logo/Nexora_Logo_Transparent.png" class="logo" alt="Logo">
            <img src="logo/Banner Name White.png" class="banner" alt="Banner">
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
        
        <!-- Top Navigation -->
        <header class="header">
            <a href="signIn.html" class="nexora-logo" id="headerLogoBtn">
                <img src="logo/Banner Transparent.png" alt="Nexora Logo">
            </a>
            
            <a href="#" class="header-contact-btn">Contact Us</a>
        </header>
        
        <!-- Main Area (Image Container) -->
        <main class="page-container">
            <!-- Full Content Image -->
            <img src="bg/contactus.png" alt="Get Started With Our ERP" class="content-img">
            
            <!-- Request a Demo Button -->
            <button class="demo-btn">Request a Demo</button>
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
    // (Note: This checks if the buttons exist first, so it works safely on both pages)
    
    const signInBtn = document.getElementById("signInBtn");
    const contactBtn = document.getElementById("contactBtn");
    const headerLogoBtn = document.getElementById("headerLogoBtn");

    if (signInBtn) signInBtn.addEventListener("click", (e) => smoothExit(e, "signIn.html"));
    if (contactBtn) contactBtn.addEventListener("click", (e) => smoothExit(e, "contactus.html"));
    if (headerLogoBtn) headerLogoBtn.addEventListener("click", (e) => smoothExit(e, "signIn.html"));
</script>

</body>
</html>