<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Nexora ERP — Procurement Suite')</title>
<link rel="stylesheet" href="{{ asset('css/procurement.css') }}">
<script>
  // Apply the saved theme before first paint to avoid a light/dark flash.
  (function(){ try { var t = localStorage.getItem('procurement-theme'); if(t) document.documentElement.setAttribute('data-theme', t); } catch(e){} })();
</script>
</head>
<body>

@include('procurement::partials.topnav')

<div class="app">
  @include('procurement::partials.sidebar')

  <main class="main">
    @yield('content')
  </main>
</div>

@include('procurement::partials.modals')
@include('procurement::partials.toast')

<script>
  window.procurementUrl = (path = '') => {
    const base = @json(url('procurement'));
    return `${base}/${String(path).replace(/^\/+/, '')}`;
  };
</script>

{{-- Shared scripts, split by concern so no single file is too long --}}
<script src="{{ asset('js/Procurement_JS/app-core.js') }}"></script>       {{-- page/notif/toast/stat helpers --}}
<script src="{{ asset('js/Procurement_JS/app-modals.js') }}"></script>     {{-- view/edit/delete record modals --}}
<script src="{{ asset('js/Procurement_JS/app-dashboard.js') }}"></script>  {{-- donut chart, report ranges, queue tabs --}}
<script src="{{ asset('js/Procurement_JS/app-filters.js') }}"></script>    {{-- table search, sort, filter panels --}}
<script src="{{ asset('js/Procurement_JS/app-deliveries.js') }}"></script> {{-- delivery tracking modal --}}
<script src="{{ asset('js/Procurement_JS/app-forms.js') }}"></script>      {{-- add PO/Supplier/Req/Delivery/Invoice forms --}}

{{-- Page-specific scripts (optional) --}}
@yield('scripts')

{{-- Final init calls (row buttons, tab counts, donut, dashboard animation) --}}
<script src="{{ asset('js/Procurement_JS/app-init.js') }}"></script>
</body>
</html>
