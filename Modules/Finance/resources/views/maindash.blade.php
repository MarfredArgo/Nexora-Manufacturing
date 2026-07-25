<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora Finance and Accounting</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body{
    background:#091d42;
    color:#fff;
}

.container{
    display:flex;
    height:100vh;
}
    /*==========================
            HEADER
    ===========================*/
        .header {
            height: 128px;
            background: #0B1E3D;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            width: 100%;
            border-bottom: 2px solid #1B3A6B;
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
                /* Dropdown */
        .dropdown-menu{
            position:absolute;
            top:60px;
            right:0;
            width:180px;
            background:#132C55;
            border:1px solid rgba(255,255,255,.08);
            border-radius:12px;
            overflow:hidden;
            display:none;
            box-shadow:0 15px 35px rgba(0,0,0,.35);
            z-index:999;
        }

        .dropdown-menu a,
        .dropdown-menu button{
            width:100%;
            display:block;
            padding:14px 18px;
            color:white;
            text-decoration:none;
            background:none;
            border:none;
            text-align:left;
            font-size:20px;
            font-family:'Inter', sans-serif;
            cursor:pointer;
            transition:.2s;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover{
            background:#4A9EE8;
        }

        .dropdown-menu hr{
            border:none;
            border-top:1px solid rgba(255,255,255,.1);
        }

        /* Show menu */
        .user-menu.open .dropdown-menu{
            display:block;
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
            SIDEBAR
        ===========================*/
        .sidebar{
        width:355px;
        background:#0d2147;
        padding-LEFT:41px;
        padding-right:41px;
        border-right:1px solid rgba(255,255,255,.08);
        }
        .menu{
        list-style:none;
        margin-top:35px;
        }

        .menu li{
        display:flex;
        align-items:center;
        gap:10px;
        width:278px;
        height:61px;
        padding:10px 12px;
        border-radius:10px;
        cursor:pointer;
        color:#8A94A6;
        font-size:20px;
        font-family:'Inter', sans-serif;
        transition:.2s;
        }

        /* Hover */
        .menu li:hover{
            background:#4A9EE8;
            color:#fff;
        }

        /* Selected */
        .menu li.active{
            background:#4A9EE8;
            color:#fff;
        }

        .menu li.active .dash-icon,
        .menu li:hover .dash-icon{
            color:#fff;
        }
        .menu hr{
            margin:18px 0;
            border:.5px solid rgba(255,255,255,.08);
        }
        .main{
            flex:1;
            overflow:hidden;
            background:#0B1E3D;
        }

        #contentFrame{
            width:100%;
            height:100%;
            background:#0B1E3D;
        }

        .dash-icon{
            width: 30px !important;
            height: 30px !important;
            color: #1659A0;
            transition: 0.1s ease;
        }

        /* USER MENU */
        .user-menu{
            position: relative;
            margin-right: 40px;
            cursor: pointer;
        }

        .user-icon{
            width:48px !important;
            height:48px !important;
            color:white;
            margin-right: 50px;
            transition:.2s;
        }

        .user-icon:hover{
            transform:scale(1.05);
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


    </style>
</head>

<body>

    <!-- Splash Screen -->
    <div id="splash">
        <div class="circle"></div>
        <div class="brand">
            <img src="{{asset('images/Nexora_Logo_Transparent.png')}}" class="logo" alt="Logo">
            <img src="{{asset('images/Banner Name White.png')}}" class="banner" alt="Banner">
        </div>
    </div>

        <!-- Top Navigation -->
        <header class="header">
            <a href="" class="nexora-logo" id="headerLogoBtn">
                <img src="{{asset('images/Banner Transparent.png')}}" alt="Nexora Logo">
            </a>

        <!-- TOP RIGHT MENU USER -->
    <div class="user-menu">
    <x-heroicon-s-user-circle class="user-icon" />

    <div class="dropdown-menu">
        <a href="">Settings</a>

        <hr>
                                    <!--USE POST INSTEAD OF GET FOR SECURITY????-->
        <form action="" method="GET">
            @csrf
            <button type="submit">Log Out</button>
        </form>
    </div>
    </div>
</header>


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

    const userMenu = document.querySelector(".user-menu");

if (userMenu) {
    userMenu.addEventListener("click", function (e) {
        e.stopPropagation();
        userMenu.classList.toggle("open");
    });

    document.addEventListener("click", function () {
        userMenu.classList.remove("open");
    });
}
//this part loads the content on the pain
function loadPage(page){
    document.getElementById("contentFrame").src = page;
}
function changePage(element, page) {

    // Remove active from every menu item
    document.querySelectorAll(".menu li").forEach(item => {
        item.classList.remove("active");
    });

    // Highlight the clicked one
    element.classList.add("active");

    // Load page into iframe (if using one)
    document.getElementById("contentFrame").src = page;
}

</script>

<div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="menu">
            <li id="dashboardBtn" class="active" onclick="changePage(this, '{{ route('finance.overview') }}')"><x-heroicon-s-squares-2x2 class="dash-icon" /><span>Dashboard</span></li>
            <li  onclick="changePage(this, '{{ route('finance.invoicedash') }}')"><x-heroicon-s-chart-bar class="dash-icon" /><span>Invoice</span></li>
            <li  onclick="changePage(this, '{{ route('finance.expensesdash') }}')"><x-heroicon-s-credit-card class="dash-icon" /><span>Expenses</span></li>
            <li  onclick="changePage(this, '{{ route('finance.salesdash') }}')"><x-heroicon-s-shopping-cart class="dash-icon" /><span>Sales</span></li>
            <li  onclick="changePage(this, '{{ route('finance.cashflowdash') }}')"><x-heroicon-s-banknotes class="dash-icon" /><span>Cash Flow</span></li>
            <li  onclick="changePage(this, '{{ route('finance.accountsdash') }}')"><x-heroicon-s-scale class="dash-icon" /><span>Accounts</span></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main">


    <iframe
        id="contentFrame"
        name="contentFrame"
        src="{{ route('finance.overview') }}" >
    </iframe>



    </main>

</div>



</body>
</html>
