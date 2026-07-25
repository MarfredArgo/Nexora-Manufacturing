<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>@yield('title', 'Nexora')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/inventory.css') }}" />
        <script>if(localStorage.getItem('sidebarState')==='open'){document.documentElement.style.setProperty('--sidebar-width','250px');document.documentElement.style.setProperty('--sidebar-ml','250px');}</script>
        @stack('head')
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', sans-serif;
                background:
                    radial-gradient(1100px 620px at 8% -8%, rgba(74, 158, 232, 0.12), transparent 58%),
                    radial-gradient(900px 560px at 108% 4%, rgba(45, 212, 168, 0.07), transparent 55%),
                    #122a51;
                background-attachment: fixed;
                color: #fff;
                min-height: 100vh;
                line-height: 1.5;
                letter-spacing: 0.1px;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-rendering: optimizeLegibility;
            }
            ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: #0b1e3d; } ::-webkit-scrollbar-thumb { background: #1b3a6b; border-radius: 4px; transition: background 0.2s ease; } ::-webkit-scrollbar-thumb:hover { background: #2a4f8f; }

            /* Nexora modal system */
            .nexora-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(6, 16, 34, 0.62);
                backdrop-filter: blur(3px);
                z-index: 20;
                display: flex;
                align-items: center;
                justify-content: center;
                /* Hidden by default, revealed by .open. Pages used to repeat
                   this rule (and re-declare the overlay colour inline), which
                   is why the backdrop differed from screen to screen. */
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
            }

            .nexora-modal-overlay.open {
                opacity: 1;
                pointer-events: auto;
            }

            /* Entry animations run only when the modal is opened (.open),
               never on page load. The overlay is always present in the DOM
               and hidden via each page's `opacity: 0` rule; putting the
               animation on the base element made it override that hidden
               state on every navigation, causing a brief modal flash. */
            .nexora-modal-overlay.open {
                animation: nexoraOverlayIn 0.2s ease-out;
            }

            .nexora-modal {
                background: linear-gradient(160deg, #16305a 0%, #132B52 55%, #0f2447 100%);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 16px;
                padding: 28px;
                width: 100%;
                max-width: 720px;
                margin: 16px;
                box-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.60);
                color: #fff;
                position: relative;
                overflow: hidden;
            }

            .nexora-modal-overlay.open .nexora-modal {
                animation: nexoraModalIn 0.24s cubic-bezier(0.22, 1, 0.36, 1);
            }

            @keyframes nexoraOverlayIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes nexoraModalIn {
                from { opacity: 0; transform: translateY(12px) scale(0.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }

            .nexora-modal-logo {
                position: absolute;
                inset: 0;
                background-image: url('/images/Nexora_Logo_Transparent.png');
                background-repeat: no-repeat;
                background-position: center;
                background-size: 340px auto;
                opacity: 0.08;
                pointer-events: none;
                z-index: 0;
                transform: rotate(40deg);
            }

            .nexora-modal > *:not(.nexora-modal-logo) {
                position: relative;
                z-index: 1;
            }

            .nexora-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
            }

            /* Icon-badge + title cluster, mirroring the Request modal header so
               every dialog opens with the same visual anchor. `.req-type-icon`
               on the Request page is the origin of this look; this is its
               shared, reusable form. */
            .nexora-modal-heading {
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }

            .nexora-modal-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 9px;
                flex-shrink: 0;
                color: #90c8ff;
                background: rgba(27, 111, 200, 0.20);
            }

            .nexora-modal-icon svg {
                width: 17px;
                height: 17px;
            }

            .nexora-modal-icon-blue { color: #90c8ff; background: rgba(27, 111, 200, 0.20); }
            .nexora-modal-icon-green { color: #86efac; background: rgba(34, 197, 94, 0.18); }
            .nexora-modal-icon-red { color: #fca5a5; background: rgba(239, 68, 68, 0.18); }
            .nexora-modal-icon-amber { color: #fcd34d; background: rgba(245, 158, 11, 0.18); }
            .nexora-modal-icon-slate { color: #cbd5e1; background: rgba(148, 163, 184, 0.18); }

            .nexora-modal-title {
                font-size: 20px;
                font-weight: 700;
                color: #fff;
            }

            .nexora-modal-close {
                background: transparent;
                border: none;
                color: #fff;
                cursor: pointer;
                font-size: 24px;
                line-height: 1;
            }

            .nexora-modal-form {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            @media (max-width: 640px) {
                .nexora-modal-form {
                    grid-template-columns: 1fr;
                }
            }

            .nexora-modal-label {
                display: block;
                font-size: 12px;
                font-weight: 600;
                color: #fff;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                margin-bottom: 6px;
            }

            .nexora-modal-input,
            .nexora-modal-select {
                width: 100%;
                padding: 10px 12px;
                border-radius: 12px;
                border: 1px solid #000;
                background: rgba(255, 255, 255, 0.9);
                color: #0f172a;
                font-family: 'Inter', sans-serif;
                outline: none;
                transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
            }

            .nexora-modal-input:focus,
            .nexora-modal-select:focus {
                border-color: #1B6FC8;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(27, 111, 200, 0.18);
            }

            .nexora-modal-input:disabled {
                background: #f1f5f9;
                color: #64748b;
                cursor: not-allowed;
            }

            .nexora-modal-error {
                color: #ef4444;
                font-size: 11px;
                margin-top: 4px;
            }

            .nexora-modal-actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                margin-top: 24px;
            }

            .nexora-modal-btn-secondary {
                background: transparent;
                color: #fff;
                border: 1px solid rgba(255, 255, 255, 0.7);
                border-radius: 10px;
                padding: 10px 18px;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.16s ease, border-color 0.16s ease;
            }

            .nexora-modal-btn-secondary:hover {
                background: rgba(255, 255, 255, 0.10);
                border-color: #fff;
            }

            .nexora-modal-btn-primary {
                background: #fff;
                color: #1B6FC8;
                border: 1px solid #000;
                border-radius: 10px;
                padding: 10px 18px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.45);
                transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            }

            .nexora-modal-btn-primary:hover {
                transform: translateY(-1px);
                background: #f4f8ff;
                box-shadow: 0 8px 18px -6px rgba(0, 0, 0, 0.5);
            }

            .nexora-modal-btn-primary:active {
                transform: translateY(0);
            }

            /* Destructive / confirming modal actions. Same shape as the primary
               button so a confirm dialog reads as part of the same family. */
            .nexora-modal-btn-danger {
                background: #dc2626;
                color: #fff;
                border-color: #b91c1c;
            }

            .nexora-modal-btn-danger:hover {
                background: #b91c1c;
            }

            .nexora-modal-btn-success {
                background: #15803d;
                color: #fff;
                border-color: #166534;
            }

            .nexora-modal-btn-success:hover {
                background: #166534;
            }

            .nexora-modal-btn-primary:disabled,
            .nexora-modal-btn-secondary:disabled {
                opacity: 0.55;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            .nexora-modal-close {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.16s ease, color 0.16s ease;
            }

            .nexora-modal-close:hover {
                background: rgba(255, 255, 255, 0.12);
            }

            /* Confirmation dialogs are narrower than the form modals. */
            .nexora-modal-sm { max-width: 460px; }
            .nexora-modal-md { max-width: 560px; }

            .nexora-modal-text {
                font-size: 14px;
                line-height: 1.6;
                color: rgba(255, 255, 255, 0.82);
            }

            .nexora-modal-form-full { grid-column: 1 / -1; }

            .nexora-modal-textarea {
                width: 100%;
                min-height: 96px;
                resize: vertical;
                padding: 10px 12px;
                border-radius: 12px;
                border: 1px solid #000;
                background: rgba(255, 255, 255, 0.9);
                color: #0f172a;
                font-family: 'Inter', sans-serif;
                font-size: 14px;
                outline: none;
                transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
            }

            .nexora-modal-textarea:focus {
                border-color: #1B6FC8;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(27, 111, 200, 0.18);
            }

            .nexora-modal-input:focus-visible,
            .nexora-modal-select:focus-visible,
            .nexora-modal-textarea:focus-visible,
            .nexora-modal-close:focus-visible,
            .nexora-modal-btn-primary:focus-visible,
            .nexora-modal-btn-secondary:focus-visible {
                outline: 2px solid #7cc0ff;
                outline-offset: 2px;
            }

            /* ── Inventory button system ──────────────────────────────────
               One base plus semantic variants. Buttons across the module use
               these instead of per-page inline colours and padding, so size,
               radius, focus ring, and motion stay identical everywhere. */
            .inv-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-family: 'Inter', sans-serif;
                font-size: 14px;
                font-weight: 600;
                line-height: 1;
                padding: 10px 20px;
                border-radius: 10px;
                border: 1px solid transparent;
                cursor: pointer;
                white-space: nowrap;
                text-decoration: none;
                transition: background-color 0.16s ease, border-color 0.16s ease,
                            color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
            }

            .inv-btn svg { flex-shrink: 0; }

            .inv-btn:focus-visible {
                outline: 2px solid #7cc0ff;
                outline-offset: 2px;
            }

            .inv-btn:disabled,
            .inv-btn[aria-disabled="true"] {
                opacity: 0.55;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
            }

            .inv-btn-primary {
                background: #1b6fc8;
                color: #fff;
                box-shadow: 0 4px 12px -6px rgba(27, 111, 200, 0.8);
            }
            .inv-btn-primary:hover:not(:disabled) {
                background: #1a63b3;
                transform: translateY(-1px);
                box-shadow: 0 9px 20px -8px rgba(27, 111, 200, 0.9);
            }
            .inv-btn-primary:active { transform: translateY(0); }

            .inv-btn-success { background: #166534; color: #fff; }
            .inv-btn-success:hover:not(:disabled) { background: #14532d; }

            .inv-btn-danger { background: #991b1b; color: #fff; }
            .inv-btn-danger:hover:not(:disabled) { background: #7f1d1d; }

            .inv-btn-neutral { background: #475569; color: #fff; }
            .inv-btn-neutral:hover:not(:disabled) { background: #334155; }

            /* Outline variants sit on the white table/card surfaces. */
            .inv-btn-outline {
                background: #fff;
                color: #64748b;
                border-color: #e2e8f0;
            }
            .inv-btn-outline:hover:not(:disabled) {
                background: #f8fafc;
                color: #0f172a;
                border-color: #cbd5e1;
            }

            .inv-btn-outline-danger {
                background: #fff;
                color: #dc2626;
                border-color: #fee2e2;
            }
            .inv-btn-outline-danger:hover:not(:disabled) {
                background: #fef2f2;
                border-color: #fecaca;
            }

            .inv-btn-quiet-danger {
                background: transparent;
                color: #dc2626;
                border-color: transparent;
            }
            .inv-btn-quiet-danger:hover:not(:disabled) { background: #fef2f2; }

            /* Sizes */
            .inv-btn-sm { font-size: 12px; padding: 7px 14px; border-radius: 8px; gap: 6px; }
            .inv-btn-xs { font-size: 11px; padding: 5px 12px; border-radius: 7px; gap: 5px; }
            .inv-btn-icon { padding: 7px; border-radius: 8px; gap: 0; }
            .inv-btn-icon.inv-btn-sm { padding: 5px; border-radius: 7px; }

            /* Segmented tab control on light panels (catalog, receiving). */
            .inv-tabs {
                display: inline-flex;
                gap: 4px;
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                border-radius: 9px;
                padding: 4px;
            }

            .inv-tab {
                padding: 6px 16px;
                border: none;
                border-radius: 6px;
                font-family: 'Inter', sans-serif;
                font-size: 12px;
                font-weight: 600;
                background: transparent;
                color: #64748b;
                cursor: pointer;
                transition: background-color 0.16s ease, color 0.16s ease;
            }

            .inv-tab:hover { color: #0f172a; }
            .inv-tab.active { background: #0b1e3d; color: #fff; }
            .inv-tab:focus-visible { outline: 2px solid #4a9ee8; outline-offset: 2px; }

            /* Segmented control on the dark page background (warehouse views). */
            .inv-segment {
                display: inline-flex;
                gap: 4px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.10);
                border-radius: 10px;
                padding: 4px;
            }

            .inv-segment button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 11px;
                border: none;
                border-radius: 7px;
                background: transparent;
                color: #94a3b8;
                cursor: pointer;
                transition: background-color 0.16s ease, color 0.16s ease;
            }

            .inv-segment button:hover { color: #e2e8f0; background: rgba(255, 255, 255, 0.06); }
            .inv-segment button.active { background: #1b6fc8; color: #fff; }
            .inv-segment button:focus-visible { outline: 2px solid #7cc0ff; outline-offset: 2px; }

            /* Row action clusters keep consistent spacing wherever they appear. */
            .inv-actions { display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap; }

            /* Pagination */
            [role="navigation"][aria-label*="Pagination"] { margin-top:0; }
            [role="navigation"][aria-label*="Pagination"] > div:last-child { display:flex !important; justify-content:space-between !important; align-items:center !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse { gap:4px !important; display:flex !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > a, [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-current="page"], [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-disabled="true"] { display:inline-flex; align-items:center; justify-content:center; padding:5px 11px !important; border-radius:8px !important; font-size:12px; font-weight:600; font-family:'Inter',sans-serif; color:#64748b !important; background:#fff !important; border:1px solid #e2e8f0 !important; min-width:32px; min-height:32px; line-height:1; box-sizing:border-box; text-decoration:none; transition:all 0.15s ease; margin:0 !important; box-shadow:none !important; cursor:pointer; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-current="page"] { background:#0b1e3d !important; color:#fff !important; border-color:#0b1e3d !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-current="page"] > span { background:transparent !important; color:inherit !important; border:0 !important; padding:0 !important; margin:0 !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > a:hover { background:#f1f5f9 !important; border-color:#cbd5e1 !important; color:#0f172a !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-disabled="true"] { opacity:0.35; cursor:default; background:#fff !important; border-color:#e2e8f0 !important; color:#cbd5e1 !important; }
            [role="navigation"][aria-label*="Pagination"] .inline-flex.rtl\:flex-row-reverse > span[aria-disabled="true"] svg { color:#cbd5e1 !important; }
            [role="navigation"][aria-label*="Pagination"] svg.w-5.h-5 { width:14px !important; height:14px !important; color:currentColor; }
            [role="navigation"][aria-label*="Pagination"] .text-sm.text-gray-700 { color:#64748b !important; font-size:12px; }
            [role="navigation"][aria-label*="Pagination"] .text-sm.text-gray-700 .font-medium { color:#0f172a; }
            [role="navigation"][aria-label*="Pagination"] .sm\:hidden { display:none !important; }

        </style>
    <style>
        /* Toast notifications */
        #toast-container {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
            pointer-events: none; max-width: 400px;
        }
        .toast {
            pointer-events: auto;
            display: flex; align-items: center; gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px; font-weight: 500; line-height: 1.4;
            box-shadow: 0 12px 32px -8px rgba(0,0,0,0.5);
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.22,1,0.36,1), opacity 0.25s ease;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.toast-success { background: #064e3b; color: #a7f3d0; border: 1px solid rgba(52,211,153,0.25); }
        .toast.toast-error { background: #450a0a; color: #fecaca; border: 1px solid rgba(248,113,113,0.25); }
        .toast-icon { flex-shrink: 0; width: 20px; height: 20px; }
        .toast-close {
            flex-shrink: 0; background: transparent; border: none; color: inherit;
            cursor: pointer; padding: 0; margin-left: auto; opacity: 0.6; font-size: 18px; line-height: 1;
        }
        .toast-close:hover { opacity: 1; }
    </style>
        @stack('styles')
    </head>
    <body>
        @include('inventory::partials.header')
        <div id="main">
            <div class="sidebar-overlay" onclick="closeSidebarMobile()"></div>
            @include('inventory::partials.sidebar')
            <div id="page-content">
                @yield('content')
            </div>
        </div>
        @include('inventory::partials.sidebar-scripts')
        @stack('scripts')
        @php
            $_toasts = [];
            if (session('success')) $_toasts[] = ['type' => 'success', 'message' => session('success')];
            if (session('error')) $_toasts[] = ['type' => 'error', 'message' => session('error')];
            if (isset($errors) && $errors->any()) {
                foreach ($errors->all() as $_err) { $_toasts[] = ['type' => 'error', 'message' => $_err]; }
            }
        @endphp
        <div id="toast-container"></div>
        <script id="flash-data" type="application/json">@json($_toasts)</script>
        <script>
            function showToast(message, type) {
                type = type || 'success';
                var container = document.getElementById('toast-container');
                var el = document.createElement('div');
                el.className = 'toast toast-' + type;
                el.innerHTML = '<svg class="toast-icon" viewBox="0 0 20 20" fill="currentColor">' +
                    (type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>') +
                    '</svg><span>' + message + '</span><button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
                container.appendChild(el);
                requestAnimationFrame(function () { el.classList.add('show'); });
                setTimeout(function () {
                    el.classList.remove('show');
                    setTimeout(function () { if (el.parentElement) el.remove(); }, 300);
                }, 4000);
                el.querySelector('.toast-close').addEventListener('click', function () { el.remove(); });
            }

            document.addEventListener('DOMContentLoaded', function () {
                var script = document.getElementById('flash-data');
                if (script) {
                    try {
                        var flashes = JSON.parse(script.textContent || '[]');
                        flashes.forEach(function (f) { showToast(f.message, f.type); });
                    } catch(e) {}
                }
            });
        </script>
    </body>
</html>
