<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Google Font -->
    
    <style type="text/css">@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin/opsz/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek/opsz/normal.woff2);unicode-range:U+0370-0377,U+037A-037F,U+0384-038A,U+038C,U+038E-03A1,U+03A3-03FF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic/opsz/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic-ext/opsz/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek-ext/opsz/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin-ext/opsz/normal.woff2);unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}@font-face {font-family:Inter;font-style:normal;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/vietnamese/opsz/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic/opsz/italic.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek-ext/opsz/italic.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin/opsz/italic.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/vietnamese/opsz/italic.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/greek/opsz/italic.woff2);unicode-range:U+0370-0377,U+037A-037F,U+0384-038A,U+038C,U+038E-03A1,U+03A3-03FF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/latin-ext/opsz/italic.woff2);unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}@font-face {font-family:Inter;font-style:italic;font-weight:100 900;src:url(/cf-fonts/v/inter/5.2.8/cyrillic-ext/opsz/italic.woff2);unicode-range:U+0460-052F,U+1C80-1C8A,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}</style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>

<body class="font-sans m-0 bg-[#1B3A6B] text-white">

   <!-- =====================================================
            TOP NAVBAR
        ====================================================== -->
    @include('partials.navbar')

    <!-- =====================================================
            DEPARTMENT CARDS
        ====================================================== -->
    <div class="ml-[60px] mr-10 mt-5 min-h-[750px] max-w-[1801px] rounded-[22px] bg-[#122A58] px-0 py-[70px] shadow-[inset_5px_10px_18px_rgba(191,0,0,.03),inset_1px_0_1px_rgba(0,0,0,.20),0_18px_35px_rgba(0,0,0,.35)]">

        <div class="grid grid-cols-[repeat(3,480px)] justify-center gap-[50px]">

            <!-- BUSINESS INTELLIGENCE -->
            <a href="{{ route('hr.departments.show', 'business-intelligence') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg width="70px" height="70px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 10C3 9.06812 3 8.60218 3.15224 8.23463C3.35523 7.74458 3.74458 7.35523 4.23463 7.15224C4.60218 7 5.06812 7 6 7V7H18V7C18.9319 7 19.3978 7 19.7654 7.15224C20.2554 7.35523 20.6448 7.74458 20.8478 8.23463C21 8.60218 21 9.06812 21 10V17C21 18.8856 21 19.8284 20.4142 20.4142C19.8284 21 18.8856 21 17 21H16H8H7C5.11438 21 4.17157 21 3.58579 20.4142C3 19.8284 3 18.8856 3 17V10Z" stroke="#ffffff" stroke-width="0.9600000000000002" stroke-linejoin="round"></path> <path d="M8 7V5C8 3.89543 8.89543 3 10 3H14C15.1046 3 16 3.89543 16 5V7" stroke="#ffffff" stroke-width="0.9600000000000002" stroke-linejoin="round"></path> <path d="M3 10C3.18614 11.3166 3.73499 12.5338 4.55558 13.5714C6.19462 15.644 8.91777 17 12 17C15.0822 17 17.8054 15.644 19.4444 13.5714C20.265 12.5338 20.8139 11.3166 21 10" stroke="#ffffff" stroke-width="0.9600000000000002" stroke-linecap="round"></path> <path d="M11.5 13H12.5" stroke="#ffffff" stroke-width="0.9600000000000002" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                <h3 class="text-[28px] font-normal tracking-wide">BUSINESS INTELLIGENCE</h3>
                <small class="text-[15px] opacity-60">001</small>
            </a>

            <!-- E-COMMERCE -->
            <a href="{{ route('hr.departments.show', 'e-commerce') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
           <svg fill="#ffffff" height="70px" width="70px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 427.286 427.286" xml:space="preserve" stroke="#ffffff" stroke-width="0.004272860000000001"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M395.579,50.143H31.707C14.224,50.143,0,64.366,0,81.85v214.558c0,17.483,14.224,31.707,31.707,31.707H171.54 l-10.559,34.028h-11.072c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h16.597h100.966h16.597c4.143,0,7.5-3.358,7.5-7.5 c0-4.142-3.357-7.5-7.5-7.5h-11.071l-10.559-34.028h133.14c17.483,0,31.707-14.224,31.707-31.707V81.85 C427.286,64.366,413.062,50.143,395.579,50.143z M176.686,362.143l10.559-34.028h59.487l10.56,34.028H176.686z M412.286,296.408 c0,9.212-7.495,16.707-16.707,16.707h-143.32H181.72H31.707c-9.212,0-16.707-7.495-16.707-16.707v-10.886h397.286V296.408z M412.286,270.522H15V81.85c0-9.212,7.495-16.707,16.707-16.707h363.872c9.212,0,16.707,7.495,16.707,16.707V270.522z"></path> </g> </g> <g> <g> <path d="M185.879,197.248c-13.872,0-25.158,11.286-25.158,25.157c0,13.872,11.286,25.158,25.158,25.158 s25.158-11.286,25.158-25.158C211.037,208.534,199.751,197.248,185.879,197.248z M185.879,232.563 c-5.601,0-10.158-4.557-10.158-10.158c0-5.601,4.557-10.157,10.158-10.157c5.601,0,10.158,4.557,10.158,10.157 C196.037,228.007,191.48,232.563,185.879,232.563z"></path> </g> </g> <g> <g> <path d="M250.646,197.248c-13.872,0-25.157,11.286-25.157,25.157c0,13.872,11.285,25.158,25.157,25.158 s25.157-11.286,25.157-25.158C275.803,208.534,264.518,197.248,250.646,197.248z M250.646,232.563 c-5.601,0-10.157-4.557-10.157-10.158c0-5.601,4.557-10.157,10.157-10.157s10.157,4.557,10.157,10.157 C260.803,228.007,256.247,232.563,250.646,232.563z"></path> </g> </g> <g> <g> <path d="M288.077,113.811c-1.425-1.631-3.484-2.567-5.649-2.567H165.206l-2.382-14.372c-0.6-3.619-3.73-6.274-7.399-6.274h-29.566 c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.357,7.499,7.499,7.499h23.207l2.364,14.262l9.426,69.326c0.505,3.718,3.68,6.49,7.432,6.49 h104.7c3.752,0,6.926-2.772,7.432-6.49l9.44-69.431C290.151,117.608,289.501,115.441,288.077,113.811z M266.438,180.674h-91.602 l-7.401-54.431h106.403L266.438,180.674z"></path> </g> </g> </g></svg>
                <h3 class="text-[28px] font-normal tracking-wide">ELECTRONIC COMMERCE</h3>
                <small class="text-[15px] opacity-60">002</small>
            </a>

            <!-- FINANCE -->
            <a href="{{ route('hr.departments.show', 'finance') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg width="70px" height="70px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" stroke="#ffffff" stroke-width="1.8719999999999999"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><style>.a{fill:none;stroke:#ffffff;stroke-linecap:round;stroke-linejoin:round;}</style></defs><circle class="a" cx="24" cy="24" r="19.5"></circle><path class="a" d="M18.135,31.36a5.4836,5.4836,0,0,0,4.6,2.07h2.76a4.6,4.6,0,0,0,0-9.2h-2.99a4.6,4.6,0,1,1,0-9.2h2.76c2.07,0,3.45.46,4.6,2.07m-5.98-4.6v23"></path></g></svg>
                <h3 class="text-[28px] font-normal tracking-wide">FINANCE</h3>
                <small class="text-[15px] opacity-60">003</small>
            </a>

            <!-- HR -->
            <a href="{{ route('hr.departments.show', 'human-resources') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" stroke-width="0.00024000000000000003"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M17.5291 7.77C17.4591 7.76 17.3891 7.76 17.3191 7.77C15.7691 7.72 14.5391 6.45 14.5391 4.89C14.5391 3.3 15.8291 2 17.4291 2C19.0191 2 20.3191 3.29 20.3191 4.89C20.3091 6.45 19.0791 7.72 17.5291 7.77Z" fill="#ffffff"></path> <path d="M20.7916 14.7004C19.6716 15.4504 18.1016 15.7304 16.6516 15.5404C17.0316 14.7204 17.2316 13.8104 17.2416 12.8504C17.2416 11.8504 17.0216 10.9004 16.6016 10.0704C18.0816 9.8704 19.6516 10.1504 20.7816 10.9004C22.3616 11.9404 22.3616 13.6504 20.7916 14.7004Z" fill="#ffffff"></path> <path d="M6.44016 7.77C6.51016 7.76 6.58016 7.76 6.65016 7.77C8.20016 7.72 9.43016 6.45 9.43016 4.89C9.43016 3.29 8.14016 2 6.54016 2C4.95016 2 3.66016 3.29 3.66016 4.89C3.66016 6.45 4.89016 7.72 6.44016 7.77Z" fill="#ffffff"></path> <path d="M6.55109 12.8506C6.55109 13.8206 6.76109 14.7406 7.14109 15.5706C5.73109 15.7206 4.26109 15.4206 3.18109 14.7106C1.60109 13.6606 1.60109 11.9506 3.18109 10.9006C4.25109 10.1806 5.76109 9.89059 7.18109 10.0506C6.77109 10.8906 6.55109 11.8406 6.55109 12.8506Z" fill="#ffffff"></path> <path d="M12.1208 15.87C12.0408 15.86 11.9508 15.86 11.8608 15.87C10.0208 15.81 8.55078 14.3 8.55078 12.44C8.56078 10.54 10.0908 9 12.0008 9C13.9008 9 15.4408 10.54 15.4408 12.44C15.4308 14.3 13.9708 15.81 12.1208 15.87Z" fill="#ffffff"></path> <path d="M8.87078 17.9406C7.36078 18.9506 7.36078 20.6106 8.87078 21.6106C10.5908 22.7606 13.4108 22.7606 15.1308 21.6106C16.6408 20.6006 16.6408 18.9406 15.1308 17.9406C13.4208 16.7906 10.6008 16.7906 8.87078 17.9406Z" fill="#ffffff"></path> </g></svg>
                <h3 class="text-[28px] font-normal tracking-wide">HUMAN RESOURCES</h3>
                <small class="text-[15px] opacity-60">004</small>
            </a>

            <!-- IT -->
            <a href="{{ route('hr.departments.show', 'it') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
               <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <path d="M7 21H17M3 13H21M10 17L9 21M14 17L15 21M6.2 17H17.8C18.9201 17 19.4802 17 19.908 16.782C20.2843 16.5903 20.5903 16.2843 20.782 15.908C21 15.4802 21 14.9201 21 13.8V6.2C21 5.0799 21 4.51984 20.782 4.09202C20.5903 3.71569 20.2843 3.40973 19.908 3.21799C19.4802 3 18.9201 3 17.8 3H6.2C5.0799 3 4.51984 3 4.09202 3.21799C3.71569 3.40973 3.40973 3.71569 3.21799 4.09202C3 4.51984 3 5.07989 3 6.2V13.8C3 14.9201 3 15.4802 3.21799 15.908C3.40973 16.2843 3.71569 16.5903 4.09202 16.782C4.51984 17 5.07989 17 6.2 17Z"
            stroke="#ffffff"
            stroke-width="1"
            stroke-linecap="round"
            stroke-linejoin="round">
        </path>
    </g>
</svg>
                <h3 class="text-[28px] font-normal tracking-wide">IT SERVICE MANAGEMENT</h3>
                <small class="text-[15px] opacity-60">005</small>
            </a>

            <!-- INVENTORY -->
            <a href="{{ route('hr.departments.show', 'inventory') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg width="60px" height="60px" viewBox="0 0 21 21" xmlns="http://www.w3.org/2000/svg">
    <rect x="2" y="2" width="17" height="14"
          fill="none"
          stroke="#ffffff"
          stroke-width="1"
          rx="0.5"/>

    <line x1="5" y1="6" x2="16" y2="6"
          stroke="#ffffff"
          stroke-width="1"
          stroke-linecap="round"/>
</svg>
                <h3 class="text-[28px] font-normal tracking-wide">INVENTORY MANAGEMENT</h3>
                <small class="text-[15px] opacity-60">006</small>
            </a>

            <!-- ORDER MANAGEMENT -->
            <a href="{{ route('hr.departments.show', 'order') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z"
            stroke="#ffffff" stroke-width="1"></path>

        <path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z"
            stroke="#ffffff" stroke-width="1"></path>

        <path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H12M16.2404 15.5C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H11.0235M4.95808 6.12H7"
            stroke="#ffffff" stroke-width="1" stroke-linecap="round"></path>
    </g>
</svg>
                <h3 class="text-[28px] font-normal tracking-wide">ORDER MANAGEMENT</h3>
                <small class="text-[15px] opacity-60">007</small>
            </a>

            <!-- PROCUREMENT -->
            <a href="{{ route('hr.departments.show', 'procurement') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
                <svg fill="#ffffff" width="70px" height="70px" viewBox="0 0 24 24" id="note-check" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color" stroke="#ffffff">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <rect id="secondary" x="9" y="3" width="6" height="3" rx="1"
            style="fill: none; stroke: #ffffff; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1;">
        </rect>

        <polyline id="secondary-2" data-name="secondary" points="9 13 11 15 15 11"
            style="fill: none; stroke: #ffffff; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1;">
        </polyline>

        <path id="primary" d="M18,4a1,1,0,0,1,1,1V20a1,1,0,0,1-1,1H6a1,1,0,0,1-1-1V5A1,1,0,0,1,6,4"
            style="fill: none; stroke: #ffffff; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1;">
        </path>
    </g>
</svg>
                <h3 class="text-[28px] font-normal tracking-wide">PROCUREMENT MANAGEMENT</h3>
                <small class="text-[15px] opacity-60">008</small>
            </a>

            <!-- PRODUCTION -->
            <a href="{{ route('hr.departments.show', 'production') }}"
                class="flex h-[190px] w-[480px] flex-col items-center justify-center gap-2 rounded-[15px] border border-white/[.08] bg-[#0B1E3D] text-center text-white no-underline transition duration-300 hover:-translate-y-1 hover:bg-[#132B52] hover:shadow-[0_10px_25px_rgba(0,0,0,.3)]">
               <svg width="70px" height="70px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier">
        <path d="M10.255 4.18806C9.84269 5.17755 8.68655 5.62456 7.71327 5.17535C6.10289 4.4321 4.4321 6.10289 5.17535 7.71327C5.62456 8.68655 5.17755 9.84269 4.18806 10.255C2.63693 10.9013 2.63693 13.0987 4.18806 13.745C5.17755 14.1573 5.62456 15.3135 5.17535 16.2867C4.4321 17.8971 6.10289 19.5679 7.71327 18.8246C8.68655 18.3754 9.84269 18.8224 10.255 19.8119C10.9013 21.3631 13.0987 21.3631 13.745 19.8119C14.1573 18.8224 15.3135 18.3754 16.2867 18.8246C17.8971 19.5679 19.5679 17.8971 18.8246 16.2867C18.3754 15.3135 18.8224 14.1573 19.8119 13.745C21.3631 13.0987 21.3631 10.9013 19.8119 10.255C18.8224 9.84269 18.3754 8.68655 18.8246 7.71327C19.5679 6.10289 17.8971 4.4321 16.2867 5.17535C15.3135 5.62456 14.1573 5.17755 13.745 4.18806C13.0987 2.63693 10.9013 2.63693 10.255 4.18806Z"
            stroke="#ffffff"
            stroke-width="1"
            stroke-linecap="round"
            stroke-linejoin="round" />

        <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z"
            stroke="#ffffff"
            stroke-width="1" />
    </g>
</svg>
                <h3 class="text-[28px] font-normal tracking-wide">PRODUCTION MANAGEMENT</h3>
                <small class="text-[15px] opacity-60">009</small>
            </a>

        </div>

    </div>

</body>

</html>
