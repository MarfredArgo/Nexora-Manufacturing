<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Nexora Packing</title>
<style>
  :root {
    --bg-header: #0B1E3D;
    --bg-dark: #1B3A6B;
    --bg-card: #0B1E3D;
    --text-light: #FFFFFF;
    --text-muted: #9FB3D1;
    --border-soft: rgba(255,255,255,0.08);
    --accent: #3B82F6;
    --pill: #16305c;
    --pill-border: #2c4373;
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: var(--bg-dark);
    color: var(--text-light);
  }

  /* ===== Navbar ===== */
  .navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 40px;
    background: var(--bg-header);
    border-bottom: 1px solid var(--border-soft);
  }

.brand{
    display:flex;
    align-items:center;
    gap:14px;
}

.brand-logo{
    display:flex;
    align-items:center;
    gap:14px;
    text-decoration:none;
    color:inherit;
}

.brand-logo .title{
    color:#FFFFFF;
}

.brand-logo .subtitle{
    color:#3B82F6;
}

  .logo {
    width: 46px;
    height: 50px;
    object-fit: contain;
  }

  .brand-text .title {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 1px;
  }

  .brand-text .subtitle {
    font-size: 11px;
    color: #3B82F6;
    letter-spacing: 1px;
  }

  .nav-links {
    display: flex;
    gap: 36px;
  }

  .nav-links a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
  }

  .nav-links a.active {
    color: var(--text-light);
    font-weight: 700;
  }

  .nav-links a:hover {
    color: var(--text-light);
    text-shadow: 0 0 0.4px currentColor, 0 0 0.4px currentColor;
  }

  /* ===== Stats Row ===== */
  .stats-row {
    display: flex;
    gap: 24px;
    padding: 32px 40px 10px;
    flex-wrap: wrap;
  }

  .stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    padding: 22px 28px;
    flex: 1;
    min-width: 200px;
  }

  .stat-card .label {
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
  }

  .stat-card .value {
    font-size: 32px;
    font-weight: 700;
  }

  /* ---------- Main Content ---------- */
  .content {
    display: flex;
    gap: 24px;
    padding: 28px 40px 60px 40px;
  }

  .panel {
    background: var(--bg-card);
    border-radius: 12px;
    overflow: hidden;
  }

  .order-queue {
    flex: 2.5;
    display: flex;
    flex-direction: column;
    /* Fixed frame: panel height never grows past this, queue scrolls inside it */
    height: 560px;
  }


  /* Scrollable body under the fixed panel header */
  .table-scroll {
    flex: 1;
    overflow-y: auto;
  }

  .table-scroll::-webkit-scrollbar {
    width: 8px;
  }
  .table-scroll::-webkit-scrollbar-track {
    background: transparent;
  }
  .table-scroll::-webkit-scrollbar-thumb {
    background: var(--pill-border);
    border-radius: 8px;
  }
  .table-scroll::-webkit-scrollbar-thumb:hover {
    background: var(--accent);
  }

  /* Keep column headers pinned while rows scroll */
  .order-queue thead th {
    position: sticky;
    top: 0;
    background: var(--bg-card);
    z-index: 5;
  }

  .panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    position: relative;
    gap: 16px;
  }

  .panel-header .title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 16px;
    white-space: nowrap;
  }

  .panel-header .actions {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-muted);
    font-size: 14px;
  }

  /* ===== Search & Filter (working controls) ===== */
  .search-wrap {
    position: relative;
  }

  .search-wrap input {
    width: 170px;
    background: var(--pill);
    border: 1px solid var(--pill-border);
    border-radius: 20px;
    padding: 8px 14px 8px 32px;
    color: var(--text-light);
    font-size: 13px;
    outline: none;
    transition: width 0.15s ease, border-color 0.15s ease;
  }

  .search-wrap input:focus {
    width: 210px;
    border-color: var(--accent);
  }

  .search-wrap input::placeholder {
    color: var(--text-muted);
  }

  .search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    font-size: 12px;
  }

  .filter-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--pill);
    border: 1px solid var(--pill-border);
    border-radius: 20px;
    padding: 8px 14px;
    color: var(--text-light);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    position: relative;
  }

  .filter-btn:hover,
  .filter-btn.active {
    border-color: var(--accent);
  }

  .filter-btn .caret {
    font-size: 10px;
    color: var(--text-muted);
    transition: transform 0.15s ease;
  }

  .filter-btn.open .caret {
    transform: rotate(180deg);
  }

  .filter-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ff2f92;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    line-height: 1.4;
    display: none;
  }

  .filter-panel {
    position: absolute;
    right: 24px;
    top: 56px;
    background: #16305c;
    border: 1px solid var(--pill-border);
    border-radius: 12px;
    padding: 14px 16px;
    width: 180px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.5);
    display: none;
    z-index: 30;
  }

  .filter-panel.show {
    display: block;
  }

  .filter-panel .filter-title {
    color: var(--text-muted);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 10px;
  }

  .filter-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0;
    cursor: pointer;
    color: var(--text-light);
    font-size: 14px;
    font-weight: 600;
    user-select: none;
  }

  .filter-option input {
    width: 16px;
    height: 16px;
    accent-color: var(--accent);
    cursor: pointer;
  }

  .filter-overlay {
    position: fixed;
    inset: 0;
    z-index: 20;
    display: none;
  }

  .filter-overlay.show {
    display: block;
  }

  .no-results-row td {
    text-align: center;
    padding: 30px;
    color: var(--text-muted);
    font-size: 14px;
  }
  /* ===== end search + filter ===== */

  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  /* Column proportions matched to the Figma design so long customer/item
     names never push the Process button off to the far right */
  table col.col-order    { width: 14%; }
  table col.col-customer { width: 20%; }
  table col.col-item     { width: 26%; }
  table col.col-qty      { width: 14%; }
  table col.col-priority { width: 16%; }
  table col.col-action   { width: 140px; }

  thead th {
    text-align: left;
    padding: 14px 24px;
    font-size: 14px;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }

  tbody td {
    padding: 14px 24px;
    font-size: 14px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  tbody tr:nth-child(even) { background: rgba(255,255,255,0.02); }

  .th-qty, .qty-cell {
    padding-left: 30px;
    text-align: center;
  }

  .th-priority, .priority-cell {
    padding-left: 30px;
    text-align: center;
  }

  .priority-low, .priority-med, .priority-high {
    margin: 0 auto;
  }

  .order-id, .product {
    color: var(--text-muted);
  }

  .th-item, .product {
    padding-left: 120px;
  }

  .th-customer, .customer {
    padding-left: 90px;
  }

  .customer {
    font-weight: 600;
  }

  .priority-low {
    background: #5A3A4A;
    color: #E8B8C8;
    padding: 3px 12px;
    border-radius: 5px;
    font-size: 11px;
    display: inline-block;
  }

  .priority-med {
    background: #6B4A1E;
    color: #FBD38D;
    padding: 3px 12px;
    border-radius: 5px;
    font-size: 11px;
    display: inline-block;
  }

  .priority-high {
    background: #7F1D2E;
    color: #FCA5B1;
    padding: 3px 12px;
    border-radius: 5px;
    font-size: 11px;
    display: inline-block;
  }

  .btn-prepare {
    display: inline-block;
    background: var(--bg-dark);
    color: var(--text-light);
    font-weight: 700;
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 20px;
    text-align: center;
    border: none;
    cursor: pointer;
  }

  .btn-prepare:hover {
    background: #244a80;
  }

  .action-cell {
    text-align: center;
    white-space: nowrap;
  }

  .empty-row td {
    height: 20px;
  }

  .activity {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 560px;
  }

  .activity-list {
    flex: 1;
    overflow-y: auto;
  }

  .activity-list::-webkit-scrollbar {
    width: 8px;
  }
  .activity-list::-webkit-scrollbar-track {
    background: transparent;
  }
  .activity-list::-webkit-scrollbar-thumb {
    background: var(--pill-border);
    border-radius: 8px;
  }

  .activity-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 14px;
  }

  .activity-item:last-child {
    border-bottom: none;
  }

  .activity-icon {
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    margin-top: 2px;
  }


  /* Blur + modal mechanism  */
  #pageContent {
    transition: filter 0.25s ease;
  }

  #pageContent.blurred {
    filter: blur(4px);
  }

  .overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(5, 12, 28, 0.45);
    align-items: center;
    justify-content: center;
    z-index: 100;
  }

  .overlay.active {
    display: flex;
  }

  .modal {
    width: 620px;
    max-width: 90vw;
    background: #16305c;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 70px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.06);
    max-height: 88vh;
    display: flex;
    flex-direction: column;
  }

  .modal-scroll {
    overflow-y: auto;
  }

  .modal-scroll::-webkit-scrollbar {
    width: 6px;
  }
  .modal-scroll::-webkit-scrollbar-track {
    background: transparent;
  }
  .modal-scroll::-webkit-scrollbar-thumb {
    background: var(--pill-border, #2c4373);
    border-radius: 6px;
  }

  .modal-header {
    background: #0f2549;
    padding: 22px 28px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
  }

  .modal-header h2 {
    margin: 0;
    color: #fff;
    font-size: 19px;
    letter-spacing: 0.2px;
  }

  .modal-header p {
    margin: 4px 0 0;
    color: #8ea3cc;
    font-size: 13px;
  }

  .modal-body {
    padding: 22px 28px 4px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 20px;
  }

.field-label {
    margin: 0 0 6px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #7691c2;
    font-weight: 700;
}

  .modal-body .field-value {
    margin: 0;
    font-size: 15px;
    color: #fff;
    font-weight: 600;
  }

  .section-label {
    margin: 0 0 10px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #7691c2;
    font-weight: 700;
    padding: 0 28px;
  }

  .items-section {
    padding: 4px 28px 20px;
  }

  .items-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .items-header .field-label {
    margin: 0;
  }

  .items-count-badge {
    background: var(--pill, #16305c);
    border: 1px solid var(--pill-border, #2c4373);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
  }

  .items-list {
    max-height: 190px;
    overflow-y: auto;
    background: #0f2549;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.05);
  }

  .items-list::-webkit-scrollbar {
    width: 6px;
  }
  .items-list::-webkit-scrollbar-track {
    background: transparent;
  }
  .items-list::-webkit-scrollbar-thumb {
    background: var(--pill-border, #2c4373);
    border-radius: 6px;
  }

  .item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
  }

  .item-row:last-child {
    border-bottom: none;
  }

  .item-name {
    font-weight: 600;
    font-size: 14px;
    color: #fff;
  }

  .item-qty {
    font-size: 12px;
    color: #8ea3cc;
    margin-top: 2px;
  }

  .item-price {
    font-weight: 700;
    font-size: 14px;
    color: #fff;
    white-space: nowrap;
  }

  .items-empty {
    padding: 16px;
    font-size: 13px;
    color: #8ea3cc;
  }

  .items-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    margin-top: 8px;
    background: #24437a;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.08);
  }

  .items-total .label {
    font-size: 13px;
    font-weight: 600;
    color: #cfdcf2;
  }

  .items-total .value {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
  }

  .box-options {
    display: flex;
    gap: 12px;
    padding: 0 28px 20px;
  }

  .box-option {
    flex: 1;
    background: #1c3766;
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #fff;
    transition: border-color 0.15s ease, background 0.15s ease, transform 0.1s ease;
  }

  .box-option:hover { background: #22406f; }
  .box-option.selected { border-color: #3B82F6; background: #24437a; }
  .box-option .box-name { font-weight: 700; font-size: 14px; }
  .box-option .box-stock { font-size: 12px; color: #9FB3D1; margin-top: 2px; }
  .box-option .box-icon { font-size: 22px; }

  .courier-options {
    display: flex;
    gap: 12px;
    padding: 0 28px 24px;
  }

  .courier-option {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 12px 18px;
    cursor: pointer;
    font-weight: 700;
    text-align: left;
    transition: border-color 0.15s ease, filter 0.15s ease;
  }

  .courier-option:hover { filter: brightness(1.05); }

  .courier-option .courier-logo {
    width: 28px;
    height: 28px;
    object-fit: contain;
    vertical-align: middle;
  }

  .courier-option .courier-name { font-size: 15px; }

  /* Exact brand colors sampled from the official logo artwork */
  .courier-option.jt { background: #FD0001; color: #fff; }
  .courier-option.flash { background: #FAEE1E; color: #111; }
  .courier-option.selected { border-color: #fff; }

  .modal-footer {
    display: flex;
    gap: 12px;
    padding: 18px 28px;
    border-top: 1px solid rgba(255,255,255,0.08);
    background: #132c54;
    flex-shrink: 0;
  }

.request-modal { width: 480px; }

.request-modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.modal-close {
  cursor: pointer;
  color: #8ea3cc;
  font-size: 16px;
}
.modal-close:hover { color: #fff; }

.request-form-body {
  padding: 20px 28px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-field { display: flex; flex-direction: column; gap: 6px; }

.form-input {
  background: #0f2549;
  border: 1px solid var(--pill-border);
  border-radius: 8px;
  padding: 10px 12px;
  color: #fff;
  font-size: 14px;
  outline: none;
  font-family: inherit;
}
.form-input:focus { border-color: var(--accent); }
.form-input::placeholder { color: #6b83ac; }

  .btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
  }

  .btn-done { background: #2b4a7c; color: #dbe4f5; }
  .btn-done:hover { background: #345a94; }

  .btn-cancel { background: #2b4a7c; color: #dbe4f5; }
  .btn-cancel:hover { background: #345a94; }

  .btn-request-material {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
  }

  .btn-request-material:hover {
    background: #2563eb;
  } 

  .error-modal {
    width: 380px;
  }

  .error-modal .modal-header {
    background: #4a1620;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .error-modal .modal-header .error-icon {
    font-size: 22px;
    line-height: 1;
  }

  .error-modal .modal-header h2 {
    color: #FCA5B1;
  }

  .error-modal-body {
    padding: 22px 28px;
    color: #dbe4f5;
    font-size: 14px;
    line-height: 1.5;
  }

  .error-modal-body .missing-material {
    color: #fff;
    font-weight: 700;
  }

  .error-modal .modal-footer {
    padding: 16px 28px;
  }

  .btn-error-ok {
    background: #7F1D2E;
    color: #fff;
  }

  .btn-error-ok:hover { background: #99283a; }

  /* Success toast — same look as the "assigned to" toast on the Shipping
     page, so the confirmation feel is consistent across tabs. */
  .assign-toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #22c55e;
    color: #08240f;
    font-weight: 700;
    font-size: 14px;
    padding: 12px 22px;
    border-radius: 8px;
    opacity: 0;
    transition: opacity 0.25s ease, transform 0.25s ease;
    z-index: 200;
    pointer-events: none;
  }

  .assign-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }

  /* ===== Nav actions (links + profile grouped on the right) ===== */
  .nav-actions {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .nav-divider {
    width: 1px;
    height: 22px;
    background: rgba(255,255,255,0.18);
  }

  /* ===== Profile menu ===== */
  .profile-menu {
    position: relative;
  }

  .profile-trigger {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-header);
    padding: 0;
  }

  .profile-trigger img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .profile-trigger:hover {
    border-color: rgba(255,255,255,0.35);
  }

  .profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    background: var(--bg-header);
    border: 1px solid var(--border-soft);
    border-radius: 10px;
    min-width: 190px;
    padding: 6px;
    display: none;
    flex-direction: column;
    box-shadow: 0 12px 28px rgba(0,0,0,0.35);
    z-index: 100;
  }

  .profile-dropdown.open {
    display: flex;
  }

  .profile-dropdown a,
  .profile-dropdown button {
    display: block;
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    color: var(--text-light);
    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    padding: 10px 12px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
  }

  .profile-dropdown a:hover,
  .profile-dropdown button:hover {
    background: rgba(255,255,255,0.08);
  }

  .profile-dropdown .divider {
    height: 1px;
    background: var(--border-soft);
    margin: 4px 0;
  }
</style>
</head>
<body>

  <div class="top-strip"></div>

  <div id="pageContent">

    <!-- Navbar -->
    <div class="navbar">
      <div class="brand brand-logo">
        <img class="logo" src="{{ asset('orderfulfillment/logo/Nexora_Logo_Transparent.png') }}" alt="Nexora Logo">
        <div class="brand-text">
            <div class="title">NEXORA</div>
            <div class="subtitle">ENTERPRISE RESOURCE PLANNING</div>
        </div>
      </div>
      <div class="nav-actions">
        <div class="nav-links">
          <a href="{{ route('order-fulfillment.dashboard') }}">Dashboard</a>
          <a href="{{ route('order-fulfillment.orders') }}">Orders</a>
          <a href="{{ route('order-fulfillment.packing') }}" class="active">Packing</a>
          <a href="{{ route('order-fulfillment.shipping') }}">Shipping</a>
          <a href="{{ route('order-fulfillment.return') }}">Returns</a>
        </div>
        <div class="nav-divider"></div>
        <div class="profile-menu" id="profileMenu">
          <button type="button" class="profile-trigger" id="profileTrigger" aria-label="Account menu">
            <img src="{{ asset('orderfulfillment/logo/pf.png') }}" alt="Profile">
          </button>
          <div class="profile-dropdown" id="profileDropdown">
            <a href="{{ route('order-fulfillment.dashboard') }}">Employee Dashboard</a>
            <div class="divider"></div>
            <form method="POST" action="{{ route('order-fulfillment.logout') }}" style="margin:0;">
              @csrf
              <button type="submit">Log out</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="label">In packing</div>
        <div class="value">{{ $inPackingCount }}</div>
      </div>
      <div class="stat-card">
        <div class="label">Shipped</div>
        <div class="value">{{ $ShippedCount }}</div>
      </div>
      <div class="stat-card">
        <div class="label">Packing Error</div>
        <div class="value">{{ $packingError }}</div>
      </div>
      <div class="stat-card">
        <div class="label">Material low stock</div>
        <div class="value">{{ $lowStockMaterialCount }}</div>
      </div>
    </div>

    <section class="content">

      <div class="panel order-queue">
        <div class="panel-header">
          <div class="title">📦 Packing queue</div>
          <div class="actions">
            <div class="search-wrap">
              <span class="search-icon">🔍</span>
              <input type="text" id="packingSearch" placeholder="Search..." autocomplete="off">
            </div>

            <button id="filterBtn" class="filter-btn">
              Filter <span class="caret">▾</span>
              <span id="filterBadge" class="filter-badge">1</span>
            </button>

            <div id="filterPanel" class="filter-panel">
              <div class="filter-title">Priority</div>
              <label class="filter-option">
                <input type="radio" name="priorityFilter" value="" class="priority-check" checked>
                All
              </label>
              <label class="filter-option">
                <input type="radio" name="priorityFilter" value="Low" class="priority-check">
                Low
              </label>
              <label class="filter-option">
                <input type="radio" name="priorityFilter" value="Med" class="priority-check">
                Med
              </label>
              <label class="filter-option">
                <input type="radio" name="priorityFilter" value="High" class="priority-check">
                High
              </label>
            </div>
          </div>
        </div>
        <div class="table-scroll">
          <table>
            <colgroup>
              <col class="col-order">
              <col class="col-customer">
              <col class="col-item">
              <col class="col-qty">
              <col class="col-priority">
              <col class="col-action">
            </colgroup>
            <thead>
              <tr>
                <th class="th-order">Order Id</th>
                <th class="th-customer">Customer</th>
                <th class="th-item">Items</th>
                <th class="th-qty">Amount</th>
                <th class="th-priority">Priority</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="packingTableBody">
              @forelse ($packingOrdersJson as $orderId => $data)
                <tr class="packing-row"
                    data-id="{{ $orderId }}"
                    data-customer="{{ $data['customer'] }}"
                    data-item="{{ $data['item'] }}"
                    data-qty="{{ $data['qty'] }}"
                    data-priority="{{ $data['priorityKey'] }}"
                    data-priority-class="{{ $data['priorityClass'] }}"
                    data-amount="{{ $data['amount'] }}"
                    data-address="{{ $data['address'] }}">
                  <td class="order-id">{{ $orderId }}</td>
                  <td class="customer">{{ $data['customer'] }}</td>
                  <td class="product">{{ $data['itemCount'] }} {{ $data['itemCount'] == 1 ? 'item' : 'items' }}</td>
                  <td class="qty-cell">₱{{ $data['amount'] }}</td>
                  <td class="priority-cell"><span class="{{ $data['priorityClass'] }}">{{ $data['priority'] }}</span></td>
                  <td class="action-cell"><button class="btn-prepare" onclick="openPackingModal('{{ $orderId }}', this.closest('tr'))">Prepare</button></td>
                </tr>
              @empty
                <tr class="empty-row"><td colspan="6" style="text-align:center; padding:24px; color:var(--text-muted);">Nothing in packing right now.</td></tr>
              @endforelse

              <tr class="no-results-row" id="noResultsRow" style="display:none;">
                <td colspan="6">No orders match your search or filter.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="panel activity">
        <div class="panel-header">
          <div class="title">📋 Packing materials</div>
          <button class="btn-request-material" onclick="openRequestModal()">
          <span>+</span> Request material
          </button>
        </div>
        <div class="activity-list">
          @forelse ($materials as $material)
            @php
              $isLow = isset($material->stock_qty, $material->low_stock_threshold)
                  && $material->stock_qty <= $material->low_stock_threshold;
              if (!empty($material->is_box)) {
                  $icon = $material->icon ?? '📦';
              } else {
                  $icon = $material->icon ?? ($isLow ? '⚠️' : '✅');
              }
            @endphp
            <div class="activity-item">
              <span class="activity-icon">{{ $icon }}</span>
              <span>{{ $material->name }} — {{ $material->stock_label ?? ($material->stock_qty . ' left') }}</span>
            </div>
          @empty
            <div class="activity-item">
              <span class="activity-icon">📦</span>
              <span style="color: var(--text-muted);">No material data yet.</span>
            </div>
          @endforelse
        </div>
      </div>

    </section>

  </div><!-- /#pageContent -->

  <div class="overlay" id="packingOverlay">
    <div class="modal">
      <div class="modal-header">
        <h2 id="modalOrderId">—</h2>
        <p>Website order</p>
      </div>

      <div class="modal-scroll">
        <div class="modal-body">
          <div>
            <p class="field-label">Customer</p>
            <p class="field-value" id="modalCustomer">—</p>
          </div>
          <div>
            <p class="field-label">Priority</p>
            <span class="priority-low" id="modalPriority">—</span>
          </div>
        </div>

        <div class="items-section">
          <div class="items-header">
            <p class="field-label">Items</p>
            <span class="items-count-badge" id="modalItemCount">0 items</span>
          </div>
          <div class="items-list" id="modalItemsList">
            <!-- populated by openPackingModal() -->
          </div>
          <div class="items-total">
            <span class="label">Total amount</span>
            <span class="value" id="modalTotalAmount">₱0.00</span>
          </div>
        </div>

        <div class="items-section" style="padding-top: 0;">
          <p class="field-label">Delivery address</p>
          <p class="field-value" id="modalAddress">—</p>
        </div>

        <p class="section-label">Box size</p>
        <div class="box-options">
          @forelse ($boxMaterials as $box)
            <div class="box-option" data-box="{{ $box->box_size }}" onclick="selectBox(this)">
              <div>
                <div class="box-name">{{ $box->name }}</div>
                <div class="box-stock">{{ $box->stock_label ?? ($box->stock_qty . ' left') }}</div>
              </div>
              <div class="box-icon">📦</div>
            </div>
          @empty
            <div class="box-option" style="opacity:0.5; pointer-events:none;">
              <div>
                <div class="box-name">No box sizes configured</div>
                <div class="box-stock">Add rows to packing_materials</div>
              </div>
              <div class="box-icon">📦</div>
            </div>
          @endforelse
        </div>

        <p class="section-label">Courier</p>
        <div class="courier-options">
          <div class="courier-option jt" data-courier="J&T" onclick="selectCourier(this)">
            <img src="{{ asset('orderfulfillment/logo/jt-logo.png') }}" alt="J&T Express" class="courier-logo">
            <span class="courier-name">J &amp; T Express</span>
          </div>
          <div class="courier-option flash" data-courier="FLASH" onclick="selectCourier(this)">
            <img src="{{ asset('orderfulfillment/logo/flash-logo.png') }}" alt="Flash Express" class="courier-logo">
            <span class="courier-name">FLASH Express</span>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-cancel" onclick="closePackingModal()">Cancel</button>
        <button class="btn btn-done" onclick="completePacking()">Done</button>
      </div>
    </div>
  </div>

  <div class="filter-overlay" id="filterOverlay"></div>

  <div class="assign-toast" id="packToast">Order packed successfully</div>

  <div class="overlay" id="packingFailedOverlay">
    <div class="modal error-modal">
      <div class="modal-header">
        <span class="error-icon">⚠️</span>
        <div>
          <h2>Packing Failed</h2>
          <p>This order could not be packed</p>
        </div>
      </div>
      <div class="error-modal-body" id="packingFailedMessage">
        Something went wrong while packing this order.
      </div>
      <div class="modal-footer">
        <button class="btn btn-error-ok" onclick="closePackingFailedModal()">OK</button>
      </div>
    </div>
  </div>

  <div class="overlay" id="requestMaterialOverlay">
    <div class="modal request-modal">
      <div class="modal-header request-modal-header">
        <div>
          <h2>🚚 Request material</h2>
          <p>Sent to the procurement department for approval.</p>
        </div>
        <span class="modal-close" onclick="closeRequestModal()">✕</span>
      </div>

      <div class="request-form-body">
        <div class="form-row">
          <div class="form-field">
            <label class="field-label">Req number</label>
            <input type="text" id="reqNumber" class="form-input" readonly>
          </div>
        <div class="form-field">
          <label class="field-label">Date requested</label>
          <input type="date" id="reqDate" class="form-input">
        </div>
      </div>

      <div class="form-field">
        <label class="field-label">Item</label>
        <select id="reqItem" class="form-input">
          <option value="Small Box">Small Box</option>
          <option value="Medium Box">Medium Box</option>
          <option value="Large Box">Large Box</option>
          <option value="Bubble Wrap">Bubble Wrap</option>
          <option value="Packing Tape">Packing Tape</option>
          <option value="Foam Inserts">Foam Inserts</option>
          <option value="Silica Gel Packs">Silica Gel Packs</option>
          <option value="Fragile Tape">Fragile Tape</option>
        </select>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Qty</label>
          <input type="number" id="reqQty" class="form-input" min="1" value="0">
        </div>
        <div class="form-field">
          <label class="field-label">Priority</label>
          <select id="reqPriority" class="form-input">
            <option value="Low">Low</option>
            <option value="Normal" selected>Normal</option>
            <option value="Urgent">Urgent</option>
            <option value="High">High</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label class="field-label">Department</label>
          <input type="text" id="reqDepartment" class="form-input" value="Order Fullfilment">
        </div>
        <div class="form-field">
          <label class="field-label">Requested by</label>
          <input type="text" id="reqRequestedBy" class="form-input" placeholder="Your name">
        </div>
      </div>

      <div class="form-field">
        <label class="field-label">Notes</label>
        <textarea id="reqNotes" class="form-input" rows="3" placeholder="Optional notes for procurement"></textarea>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-cancel" onclick="closeRequestModal()">Cancel</button>
      <button class="btn btn-done" onclick="submitMaterialRequest()">Submit request</button>
    </div>
  </div>
</div>

  <script>
    // Order data keyed by order id, rendered straight from the DB
    // ($packingOrders, queried in the controller) — nothing hardcoded.
    const orders = @json($packingOrdersJson);
    let currentOrderId = null;
    let selectedBox = null;
    let selectedCourier = null;
    let isSubmittingPacking = false; // guards against double-click/double-submit on "Done"

    function showPackToast(message, isError = false) {
      const toast = document.getElementById('packToast');
      toast.textContent = message;
      toast.style.background = isError ? '#ef4444' : '#22c55e';
      toast.style.color = isError ? '#ffffff' : '#08240f';
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2600);
    }

    // completePacking() does a full page reload right after a successful
    // packing call (the row needs to disappear from the queue), so the
    // toast can't just be shown in-place like the Shipping page's — it
    // would be wiped out before anyone saw it. Stash the message before
    // reloading and pick it back up here once the fresh page has loaded.
    (function () {
      const pending = sessionStorage.getItem('packToastMessage');
      if (pending) {
        sessionStorage.removeItem('packToastMessage');
        showPackToast(pending);
      }
    })();

    function openRequestModal() {
      document.getElementById('reqNumber').value = 'REQ-' + String(Date.now()).slice(-5);
      document.getElementById('reqDate').value = new Date().toISOString().split('T')[0];
      document.getElementById('reqPriority').value = 'Normal';
      document.getElementById('pageContent').classList.add('blurred');
      document.getElementById('requestMaterialOverlay').classList.add('active');
    }

    function closeRequestModal() {
      document.getElementById('pageContent').classList.remove('blurred');
      document.getElementById('requestMaterialOverlay').classList.remove('active');
    }

    async function submitMaterialRequest() {
    const payload = {
      req_number: document.getElementById('reqNumber').value,
      date_requested: document.getElementById('reqDate').value,
      item: document.getElementById('reqItem').value,
      qty: document.getElementById('reqQty').value,
      priority: document.getElementById('reqPriority').value,
      department: document.getElementById('reqDepartment').value,
      requested_by: document.getElementById('reqRequestedBy').value,
      notes: document.getElementById('reqNotes').value,
    };

    if (!payload.qty || payload.qty <= 0) { alert('Enter a valid quantity'); return; }
    if (!payload.requested_by) { alert('Enter your name'); return; }

    try {
      const response = await fetch(`{{ route('order-fulfillment.material-requests.store') }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
      });

      const result = await response.json().catch(() => ({}));
      if (result.success) {
        closeRequestModal();
        showPackToast(result.message || 'Material request sent to Procurement.');
      } else {
        const validationMessage = result.errors
          ? Object.values(result.errors).flat().join('\n')
          : null;
        alert(validationMessage || result.message || `Failed to submit request (HTTP ${response.status}).`);
      }
    } catch (error) {
      console.error('Material request failed:', error);
      alert('Could not submit the material request. Please try again.');
      }
    }

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str ?? '';
      return div.innerHTML;
    }

    function renderModalItems(order, rowEl) {
      const listEl  = document.getElementById('modalItemsList');
      const countEl = document.getElementById('modalItemCount');
      const totalEl = document.getElementById('modalTotalAmount');

      // Prefer the multi-item array from the order payload. Fall back to
      // the single item/qty/amount fields (or the clicked row's data
      // attributes) so older/single-product orders still render a list.
      let items = Array.isArray(order.items) ? order.items : null;

      if (!items || items.length === 0) {
        const fallbackAmount = order.amount ?? (rowEl ? rowEl.dataset.amount : null);
        items = [{
          name: order.item ?? 'Item',
          qty: order.qty ?? 1,
          amount: fallbackAmount,
        }];
      }

      countEl.textContent = items.length + (items.length === 1 ? ' item' : ' items');

      listEl.innerHTML = items.map(function (item) {
        const price = item.amount != null ? '₱' + item.amount : '—';
        return `
          <div class="item-row">
            <div>
              <div class="item-name">${escapeHtml(item.name)}</div>
              <div class="item-qty">Qty ${escapeHtml(item.qty)}</div>
            </div>
            <div class="item-price">${price}</div>
          </div>
        `;
      }).join('');

      // Total amount for the whole order — computed server-side from the
      // real line items, falls back to the row's data-amount if missing.
      const totalAmount = order.amount ?? (rowEl ? rowEl.dataset.amount : null);
      totalEl.textContent = totalAmount != null ? '₱' + totalAmount : '—';
    }

    function openPackingModal(orderId, rowEl) {
      currentOrderId = orderId;
      const order = orders[orderId];

      console.log("Modal opened. Order ID =", orderId);
      console.log("currentOrderId =", currentOrderId);
      if (order) {
        document.getElementById('modalOrderId').textContent = orderId;
        document.getElementById('modalCustomer').textContent = order.customer;
        document.getElementById('modalAddress').textContent = order.address;

        const priorityEl = document.getElementById('modalPriority');
        priorityEl.textContent = order.priority;
        priorityEl.className = order.priorityClass;

        renderModalItems(order, rowEl);
      }

      // reset box/courier selection each time the modal opens
      document.querySelectorAll('.box-option').forEach(el => el.classList.remove('selected'));
      document.querySelectorAll('.courier-option').forEach(el => el.classList.remove('selected'));

      document.getElementById('pageContent').classList.add('blurred');
      document.getElementById('packingOverlay').classList.add('active');
    }

    function closePackingModal() {
      document.getElementById('pageContent').classList.remove('blurred');
      document.getElementById('packingOverlay').classList.remove('active');
    }

    function showPackingFailedModal(message) {
      document.getElementById('packingFailedMessage').innerHTML = message;
      document.getElementById('packingFailedOverlay').classList.add('active');
    }

    function closePackingFailedModal() {
      document.getElementById('packingFailedOverlay').classList.remove('active');
    }

    function selectBox(el) {
      document.querySelectorAll('.box-option')
        .forEach(o => o.classList.remove('selected'));

      el.classList.add('selected');

      selectedBox =
        el.querySelector('.box-name').innerText;
    }

    function selectCourier(el) {
      document.querySelectorAll('.courier-option')
        .forEach(o => o.classList.remove('selected'));

      el.classList.add('selected');

      selectedCourier =
        el.dataset.courier;
    }

    /* ===================== Search + Filter (working) ===================== */
    const packingRows    = Array.from(document.querySelectorAll('.packing-row'));
    const searchInput    = document.getElementById('packingSearch');
    const filterBtn       = document.getElementById('filterBtn');
    const filterPanel     = document.getElementById('filterPanel');
    const filterOverlay   = document.getElementById('filterOverlay');
    const filterBadge     = document.getElementById('filterBadge');
    const noResultsRow    = document.getElementById('noResultsRow');
    const priorityChecks  = document.querySelectorAll('.priority-check');

    function activePriority() {
      const checked = Array.from(priorityChecks).find(c => c.checked);
      return checked ? checked.value : '';
    }

    function applyPackingFilters() {
      const query = searchInput.value.trim().toLowerCase();
      const active = activePriority();
      let visibleCount = 0;

      packingRows.forEach(function (row) {
        const d = row.dataset;
        const haystack = [d.id, d.customer, d.item, d.address]
          .join(' ')
          .toLowerCase();

        const matchesSearch = query === '' || haystack.includes(query);
        const matchesPriority = active === '' || d.priority === active;
        const visible = matchesSearch && matchesPriority;

        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
      });

      noResultsRow.style.display = visibleCount === 0 ? '' : 'none';

      if (active !== '') {
        filterBtn.classList.add('active');
        filterBadge.style.display = 'inline-block';
        filterBadge.textContent = '1';
      } else {
        filterBtn.classList.remove('active');
        filterBadge.style.display = 'none';
      }
    }

    function openFilterPanel() {
      filterPanel.classList.add('show');
      filterOverlay.classList.add('show');
      filterBtn.classList.add('open');
    }

    function closeFilterPanel() {
      filterPanel.classList.remove('show');
      filterOverlay.classList.remove('show');
      filterBtn.classList.remove('open');
    }

    filterBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      filterPanel.classList.contains('show') ? closeFilterPanel() : openFilterPanel();
    });

    filterOverlay.addEventListener('click', closeFilterPanel);

    priorityChecks.forEach(function (c) {
      c.addEventListener('change', applyPackingFilters);
    });

    searchInput.addEventListener('input', applyPackingFilters);

    async function completePacking() {
      console.log("Sending order ID:", currentOrderId);
    if(!selectedBox)
    {
        alert('Select a box');
        return;
    }

    if(!selectedCourier)
    {
        alert('Select a courier');
        return;
    }

    // Prevent a fast double-click (or a slow request plus an impatient
    // second click) from firing this twice for the same order.
    if (isSubmittingPacking) {
        return;
    }
    isSubmittingPacking = true;

    const doneBtn = document.querySelector('#packingOverlay .btn-done');
    if (doneBtn) {
        doneBtn.disabled = true;
        doneBtn.classList.add('disabled');
    }

    try {
        const response = await fetch(
             `{{ url('/order-fulfillment/packing/process') }}/${encodeURIComponent(currentOrderId)}`,
            {
                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
                },

                body: JSON.stringify({
                    courier:selectedCourier,
                    box:selectedBox
                })
            }
        );

        let result;
        try {
            result = await response.json();
        } catch (e) {
            // Server returned something that wasn't JSON (e.g. an HTML error
            // page from an unhandled server error). Show a generic failure
            // instead of leaving the user with no feedback at all.
            showPackingFailedModal('The server returned an unexpected response. Please try again.');
            return;
        }

        if (result.success) {
            sessionStorage.setItem(
                'packToastMessage',
                `Order packed & shipped as ${result.shipment_id}`
            );
            location.reload();
            return;
        }

        if (result.error === 'insufficient_stock') {
            showPackingFailedModal(
                `Not enough <span class="missing-material">${result.material}</span> in stock to pack this order. Please restock and try again.`
            );
        } else if (result.error === 'order_not_found') {
            showPackingFailedModal('This order could not be found. It may have already been processed.');
        } else if (result.error === 'already_processed') {
            // Another request for this same order already went through
            // (e.g. an earlier click already succeeded) — refresh so the
            // queue reflects reality instead of letting the user retry
            // into a second shipment.
            showPackingFailedModal('This order is already being processed or was already shipped.');
        } else {
            showPackingFailedModal('Something went wrong while packing this order. Please try again.');
        }
    } finally {
        // Always release the guard and re-enable the button, whether the
        // request succeeded, failed, or errored — otherwise a failed
        // request would leave "Done" permanently disabled.
        isSubmittingPacking = false;
        if (doneBtn) {
            doneBtn.disabled = false;
            doneBtn.classList.remove('disabled');
        }
    }
  }
  </script>

  <script>
    (function () {
      const menu = document.getElementById('profileMenu');
      const trigger = document.getElementById('profileTrigger');
      const dropdown = document.getElementById('profileDropdown');
      if (!menu || !trigger || !dropdown) return;

      trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
      });

      document.addEventListener('click', function (e) {
        if (!menu.contains(e.target)) {
          dropdown.classList.remove('open');
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') dropdown.classList.remove('open');
      });
    })();
  </script>

</body>
</html>
