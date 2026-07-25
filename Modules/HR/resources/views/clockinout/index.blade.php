<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance</title>

<!-- Google Font: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap">

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
    };
</script>

<style>
    /* Placeholder color isn't a Tailwind utility on its own without the plugin,
       so keep this tiny rule for the Employee ID input */
    #empID::placeholder {
        color: #9bb1d3;
    }

    #attendanceBtn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
</style>
</head>

<body class="bg-[#f5f5f7] font-sans m-0 p-0">

    <!-- NAVBAR -->
    <header class="w-full h-[80px] sm:h-[108px] bg-[#1B3A6B] flex items-center px-6 sm:px-[60px] shadow-[0_2px_8px_rgba(0,0,0,.08)]">
        <img src="{{ asset('images/logo.png') }}" alt="Nexora Logo" class="h-12 sm:h-[72px] w-auto object-contain">
    </header>

    <!-- MAIN -->
    <div class="w-full min-h-[calc(100vh-80px)] sm:min-h-[calc(100vh-108px)] flex justify-center items-center px-4 py-8">

        <div class="w-full max-w-[510px] text-center">

            <div id="date" class="text-lg sm:text-2xl md:text-[25.4px] text-[#132B52] mb-[-6px] sm:mb-[-12px]"></div>

            <div id="clock"
                class="text-[72px] sm:text-[120px] md:text-[160px] lg:text-[194px] font-semibold text-[#132B52] leading-[0.9] mb-6 sm:mb-[30px] flex justify-center items-center w-full text-center">
            </div>

            <div class="mt-8 sm:mt-[60px]">

                @php
                    $attendanceButtonLabel = session('clocked_in', false) && !session('clocked_out', false)
                        ? 'â—´ Clock Out'
                        : 'â—´ Clock In';
                @endphp

                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-left text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-left text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-left text-sm text-red-800">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('hr.clockinout.index') }}" id="attendanceForm">
                    @csrf
                    <input type="hidden" name="action" id="attendanceAction" value="clock_in">
                    <input type="hidden" name="photo" id="photoData" value="">

                    <div class="w-full h-[60px] sm:h-[75px] bg-[#132B52] rounded-[10px] flex items-center px-4 sm:px-7 mb-5 sm:mb-6">
                        <span class="text-white text-base sm:text-[22px] font-medium mr-3 sm:mr-5 whitespace-nowrap">Employee ID:</span>
                        <input
                            type="text"
                            id="empID"
                            name="employee_id"
                            inputmode="numeric"
                            autocomplete="off"
                            value="{{ old('employee_id', session('employee_code', session('employee_id'))) }}"
                            class="flex-1 h-full border-none outline-none bg-transparent text-white text-base sm:text-2xl min-w-0">
                    </div>

                    <div id="captureBtn"
                        class="w-full h-[50px] bg-[#173d73] text-white rounded-md flex justify-center items-center mb-5 text-base sm:text-xl cursor-pointer select-none">
                        [â—‰] Capture Photo
                    </div>

                <video id="video" autoplay playsinline
                    class="hidden w-full rounded-[10px] my-5 mx-auto"></video>

                <canvas id="canvas" class="hidden"></canvas>

                <img id="photo" class="hidden w-full max-w-[320px] rounded-[10px] mt-5 mx-auto object-cover" alt="Captured attendance photo">

                <button id="attendanceBtn" type="button" disabled
                    class="w-full h-[80px] sm:h-[114px] border-none rounded-[7.9px] bg-[#132B52] text-white text-xl sm:text-[28.6px] font-normal cursor-pointer transition-colors duration-[250ms] mb-2 flex justify-center items-center hover:bg-[#0f2e59] disabled:hover:bg-[#132B52]">
                    @if (session('clocked_in', false) && !session('clocked_out', false))
                        <span aria-hidden="true">&#9654;</span>&nbsp;Clock Out
                    @else
                        <span aria-hidden="true">&#9654;</span>&nbsp;Clock In
                    @endif
                </button>

                <div id="requirementNote" class="text-sm sm:text-base text-[#6c757d] mb-5 sm:mb-6 text-center">
                    Enter your Employee ID and capture a photo to enable Clock In.
                </div>
            </form>

                <div class="text-lg sm:text-xl md:text-[22px] text-[#333] leading-relaxed text-left sm:text-center">
                    <div>Clock In Time: <span id="clockIn"></span></div>
                    <div>Clock Out Time: <span id="clockOut"></span></div>
                </div>

            </div>

        </div>

    </div>

<script>

const clock=document.getElementById("clock");
const date=document.getElementById("date");

function updateClock(){

    const now=new Date();

    clock.innerHTML=now.toLocaleTimeString([],{
        hour:'2-digit',
        minute:'2-digit',
        hour12:false
    });

    date.innerHTML=now.toLocaleDateString('en-US',{
        weekday:'long',
        year:'numeric',
        month:'long',
        day:'numeric'
    });

}

updateClock();
setInterval(updateClock,1000);

let clockedIn=false;
let photoCaptured=false;

const attendanceForm = document.getElementById("attendanceForm");
const btn=document.getElementById("attendanceBtn");
const empIDInput=document.getElementById("empID");
const requirementNote=document.getElementById("requirementNote");

function checkRequirements(){

    const idFilled = empIDInput.value.trim() !== "";
    const actionLabel = clockedIn ? "Clock Out" : "Clock In";

    if(idFilled && photoCaptured){
        btn.disabled = false;
        requirementNote.innerHTML = "";
    }else{
        btn.disabled = true;

        if(!idFilled && !photoCaptured){
            requirementNote.innerHTML = `Enter your Employee ID and capture a photo to enable ${actionLabel}.`;
        }else if(!idFilled){
            requirementNote.innerHTML = `Enter your Employee ID to enable ${actionLabel}.`;
        }else{
            requirementNote.innerHTML = `Capture a photo to enable ${actionLabel}.`;
        }
    }

}

empIDInput.addEventListener("input", function(){

    // Numbers only â€” strip anything that isn't a digit
    const cleaned = empIDInput.value.replace(/[^0-9]/g, "");

    if(empIDInput.value !== cleaned){
        empIDInput.value = cleaned;
    }

    checkRequirements();

});

// Block non-digit keys before they even appear (blocks letters, symbols, etc.)
empIDInput.addEventListener("keydown", function(e){

    const allowedKeys = [
        "Backspace","Delete","Tab","Escape","Enter",
        "ArrowLeft","ArrowRight","ArrowUp","ArrowDown","Home","End"
    ];

    if(allowedKeys.includes(e.key)) return;

    // Allow Ctrl/Cmd combos (copy, paste, select all, etc.)
    if(e.ctrlKey || e.metaKey) return;

    if(!/^[0-9]$/.test(e.key)){
        e.preventDefault();
    }

});

// Also sanitize pasted content (e.g. pasting "12a34" becomes "1234")
empIDInput.addEventListener("paste", function(e){

    e.preventDefault();

    const pasted = (e.clipboardData || window.clipboardData).getData("text");
    const cleaned = pasted.replace(/[^0-9]/g, "");

    const start = empIDInput.selectionStart;
    const end = empIDInput.selectionEnd;
    const current = empIDInput.value;

    empIDInput.value = current.slice(0, start) + cleaned + current.slice(end);

    checkRequirements();

});

const captureBtn = document.getElementById("captureBtn");
const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const photo = document.getElementById("photo");
const photoData = document.getElementById("photoData");

// Use HTML entities for icon glyphs so this page stays readable even if a
// source file was previously saved using an incompatible text encoding.
captureBtn.innerHTML = "&#9673;&nbsp;Capture Photo";

const serverClockIn = @json(session('clock_in'));
const serverClockOut = @json(session('clock_out'));
const serverClockedIn = @json(session('clocked_in', false));
const serverClockedOut = @json(session('clocked_out', false));

let stream = null;
let cameraOpen = false;

function compressCapture(videoEl) {
    const maxEdge = 480;
    const srcW = videoEl.videoWidth || 640;
    const srcH = videoEl.videoHeight || 480;
    const scale = Math.min(1, maxEdge / Math.max(srcW, srcH));
    const dstW = Math.max(1, Math.round(srcW * scale));
    const dstH = Math.max(1, Math.round(srcH * scale));

    canvas.width = dstW;
    canvas.height = dstH;

    const ctx = canvas.getContext("2d");
    ctx.drawImage(videoEl, 0, 0, dstW, dstH);

    // JPEG ~0.55 keeps the upload small while remaining clear enough for attendance.
    return canvas.toDataURL("image/jpeg", 0.55);
}

if (serverClockIn) {
    document.getElementById("clockIn").textContent = serverClockIn;
    clockedIn = true;
}

if (serverClockOut) {
    document.getElementById("clockOut").textContent = serverClockOut;
}

if (serverClockedOut) {
    btn.disabled = true;
    btn.innerHTML = "Completed";
    btn.classList.remove("bg-[#132B52]","hover:bg-[#0f2e59]");
    btn.classList.add("bg-[#6c757d]","cursor-not-allowed");
    empIDInput.disabled = true;
    captureBtn.classList.add("opacity-55","pointer-events-none");
} else if (clockedIn) {
    btn.innerHTML = "â—´ Clock Out";
    requirementNote.innerHTML = "Capture a photo to enable Clock Out.";
}

// This runs after the legacy label assignment above and replaces any
// previously mojibaked glyph with an encoding-safe entity.
if (clockedIn && !serverClockedOut) {
    btn.innerHTML = "&#9654;&nbsp;Clock Out";
}

captureBtn.addEventListener("click", async () => {

    if(!cameraOpen){

        try{

            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 480 } }
            });

            video.srcObject = stream;
            video.classList.remove("hidden");
            photo.classList.add("hidden");

            captureBtn.innerHTML = "Take Photo";

            cameraOpen = true;

        }catch(err){

            alert("Unable to access camera.");

        }

    }else{

        const image = compressCapture(video);

        photo.src = image;
        photo.classList.remove("hidden");
        photoData.value = image;

        video.classList.add("hidden");

        stream.getTracks().forEach(track=>track.stop());

        captureBtn.innerHTML = "Retake Photo";

        cameraOpen = false;
        photoCaptured = true;

        checkRequirements();

    }

});

btn.onclick=function(){

    const id=empIDInput.value.trim();

    if(id===""){
        alert("Please enter Employee ID.");
        return;
    }

    if(!photoCaptured || !photoData.value){
        alert("Please capture a photo before proceeding.");
        return;
    }

    if (! clockedIn) {
        if (confirm("Clock in now?")) {
            document.getElementById('attendanceAction').value = 'clock_in';
            attendanceForm.submit();
        }
    } else {
        if (confirm("Clock out now?")) {
            document.getElementById('attendanceAction').value = 'clock_out';
            attendanceForm.submit();
        }
    }

}
</script>

</body>
</html>
