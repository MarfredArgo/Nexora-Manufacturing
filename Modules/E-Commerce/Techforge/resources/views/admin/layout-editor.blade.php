@extends('ecommerce::admin.layout', ['title' => 'Edit Storefront', 'heading' => 'Edit Storefront', 'hideLayout' => true])

@php
    $storefrontCompany = request()->attributes->get('ecommerce_company');
    $store = $storefrontCompany?->ecommerce_slug ?: 'techforge';

    $sections = collect($layout['sections'] ?? [])->keyBy('id');
    $hero = $sections->get('hero', []);
    $listings = $sections->get('featured_listings', []);
    $promo = $sections->get('promo', []);
    $benefits = $sections->get('benefits', []);
    $order = implode(',', array_column($layout['sections'] ?? [], 'id'));

    $navbar = $layout['navbar'] ?? [];
    $links = $navbar['links'] ?? [];
    $customPages = $layout['custom_pages'] ?? [];

    $context = $context ?? 'home';
    $isHome = $context === 'home';
    $currentPage = collect($customPages)->firstWhere('slug', $context);

    $previewUrl = route('ecommerce.admin.layout.preview', ['context' => $context, 'preview' => 1]);
@endphp

@section('content')
<style>
    body { overflow: hidden; margin: 0; background: #f4f6f8; font-family: Inter, Arial, sans-serif; }
    .page { width: 100% !important; max-width: 100% !important; padding: 0 !important; display: flex; flex-direction: column; height: 100vh; }
    .page-heading { display: none; }
    .success { display: none; }

    /* Top Bar */
    .editor-topbar { background: #fff; height: 56px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; border-bottom: 1px solid #e1e3e5; z-index: 50; }
    .topbar-left { display: flex; align-items: center; gap: 16px; }
    .topbar-left a { color: #202223; display: flex; align-items: center; text-decoration: none; padding: 8px; border-radius: 4px; }
    .topbar-left a:hover { background: #f4f6f8; }

    .topbar-center { display: flex; align-items: center; background: #f4f6f8; border-radius: 6px; padding: 4px; }
    .topbar-center select { background: transparent; border: none; font-weight: 500; font-size: 13px; color: #202223; padding: 4px 24px 4px 8px; outline: none; appearance: none; cursor: pointer; background-image: url('data:image/svg+xml;utf8,<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23202223" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>'); background-repeat: no-repeat; background-position: right 4px center; }
    .context-badge { background: #aee9d1; color: #008060; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 4px; margin-left: 8px; }

    .topbar-right { display: flex; align-items: center; gap: 8px; }
    .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 4px; color: #5c5f62; border: none; background: transparent; cursor: pointer; }
    .icon-btn:hover { background: #f4f6f8; }
    .save-btn-top { background: #008060; color: #fff; padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; box-shadow: 0 1px 0 rgba(0,0,0,0.05); transition: all 0.15s ease; margin-left: 8px; }
    .save-btn-top:hover { background: #006e52; }
    .save-btn-top:active { transform: scale(0.95); box-shadow: none; }

    .builder-container { display: flex; height: calc(100vh - 56px); overflow: hidden; background: #f4f6f8; width: 100%; }

    /* Rich Text Editor (Used by component) */
    .rt-container { border: 1px solid #c9cccf; border-radius: 4px; background: #fff; overflow: hidden; margin-top: 4px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
    .rt-container:focus-within { border-color: #008060; box-shadow: 0 0 0 1px #008060; }
    .rt-toolbar { display: flex; align-items: center; gap: 4px; padding: 6px; border-bottom: 1px solid #e1e3e5; background: #f9fafb; flex-wrap: wrap; }
    .rt-btn { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; background: transparent; border: none; border-radius: 4px; cursor: pointer; color: #5c5f62; font-family: serif; font-size: 14px; }
    .rt-btn:hover { background: #e1e3e5; color: #202223; }
    .rt-btn.bold { font-weight: bold; font-family: sans-serif; }
    .rt-btn.italic { font-style: italic; }
    .rt-editor { padding: 12px; min-height: 80px; max-height: 200px; overflow-y: auto; font-size: 14px; color: #202223; line-height: 1.5; outline: none; background: #fff; }
    .rt-editor p { margin: 0 0 8px 0; }
    .rt-editor p:last-child { margin: 0; }
    .rt-editor h2 { font-size: 1.5em; font-weight: bold; margin: 0 0 8px 0; }
    .rt-editor ul { margin: 0 0 8px 0; padding-left: 20px; list-style-type: disc; }
    .rt-editor ol { margin: 0 0 8px 0; padding-left: 20px; list-style-type: decimal; }

    .label-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 500; color: #202223; margin-bottom: 4px; }

    /* Left Sidebar Styling */
    .builder-sidebar { width: 300px; min-width: 300px; background: #fff; color: #202223; overflow-y: auto; border-right: 1px solid #e1e3e5; display: flex; flex-direction: column; z-index: 10; }
    .builder-sidebar::-webkit-scrollbar { width: 4px; }
    .builder-sidebar::-webkit-scrollbar-thumb { background: #e1e3e5; border-radius: 4px; }

    .sidebar-header { padding: 16px; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e1e3e5; display: flex; justify-content: space-between; align-items: center; background: #fff; }

    .sidebar-group-title { padding: 16px 16px 8px; font-size: 14px; font-weight: 600; color: #202223; }

    .nav-item-wrapper { display: flex; flex-direction: column; }
    .nav-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; cursor: pointer; font-size: 13px; color: #202223; border-left: 3px solid transparent; }
    .nav-item:hover { background: #f4f6f8; }

    .nav-item-left { display: flex; align-items: center; gap: 8px; flex: 1; }
    .chevron-toggle { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; color: #5c5f62; border-radius: 4px; transition: transform 0.2s; }
    .chevron-toggle:hover { background: #e1e3e5; }
    .nav-item-wrapper.expanded .chevron-toggle { transform: rotate(90deg); }

    .nav-item-left > svg { width: 16px; height: 16px; color: #5c5f62; }

    .sub-items { display: none; flex-direction: column; padding-bottom: 8px; }
    .nav-item-wrapper.expanded .sub-items { display: flex; }

    .sub-item { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 48px; font-size: 13px; color: #202223; cursor: pointer; border-radius: 4px; margin: 2px 8px; }
    .sub-item:hover { background: #f4f6f8; }
    .sub-item.active { background: #0060df; color: #fff; font-weight: 500; }
    .sub-item.active svg { color: #fff; }
    .sub-item svg { width: 16px; height: 16px; color: #5c5f62; }

    .add-block-btn { display: flex; align-items: center; gap: 8px; color: #2c6ecb; font-size: 13px; padding: 6px 16px 6px 48px; background: transparent; border: none; cursor: pointer; width: 100%; text-align: left; }
    .add-block-btn:hover { text-decoration: underline; }
    .add-block-btn svg { color: #2c6ecb; width: 14px; height: 14px; }

    .add-section-btn { display: flex; align-items: center; gap: 8px; color: #2c6ecb; font-size: 13px; padding: 8px 16px; background: transparent; border: none; cursor: pointer; width: 100%; text-align: left; margin-left: 16px;}
    .add-section-btn:hover { text-decoration: underline; }
    .add-section-btn svg { color: #2c6ecb; width: 14px; height: 14px; }

    /* Right Sidebar (Properties Panel) */
    .builder-right-sidebar { width: 320px; min-width: 320px; background: #fff; border-left: 1px solid #e1e3e5; display: flex; flex-direction: column; z-index: 10; transition: transform 0.3s ease; transform: translateX(100%); position: absolute; right: 0; top: 56px; height: calc(100vh - 56px); }
    .builder-right-sidebar.open { transform: translateX(0); box-shadow: -4px 0 15px rgba(0,0,0,0.05); }

    .right-panel-content { display: none; flex: 1; flex-direction: column; overflow-y: auto; }
    .right-panel-content.active { display: flex; }

    .panel-header { padding: 16px; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e1e3e5; display: flex; align-items: center; justify-content: space-between; }
    .panel-header button { background: transparent; border: none; cursor: pointer; color: #5c5f62; padding: 4px; border-radius: 4px; }
    .panel-header button:hover { background: #f4f6f8; color: #202223; }

    .section-content { padding: 16px; }
    .section-content label { display: block; font-size: 13px; color: #202223; margin-bottom: 4px; font-weight: 500; margin-top: 16px; }
    .section-content label:first-child { margin-top: 0; }
    .section-content input, .section-content textarea, .section-content select { width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #c9cccf; border-radius: 4px; box-sizing: border-box; }
    .section-content input:focus, .section-content textarea:focus, .section-content select:focus { border-color: #2c6ecb; outline: 1px solid #2c6ecb; }

    /* Iframe Styling */
    .builder-preview { flex-grow: 1; position: relative; display: flex; flex-direction: column; padding: 16px; overflow: hidden; transition: margin-right 0.3s ease; }
    .builder-preview.panel-open { margin-right: 320px; }

    .builder-preview-inner { flex-grow: 1; background: #fff; border-radius: 8px; box-shadow: 0 0 0 1px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; position: relative; transform-origin: top left; transition: transform 0.3s ease; }

    .preview-header { padding: 8px 16px; border-bottom: 1px solid #e1e3e5; display: flex; justify-content: flex-end; align-items: center; background: #fff; }
    .status { font-size: 12px; color: #5c5f62; display: flex; align-items: center; gap: 8px; }

    .live-indicator { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: #2c6ecb; }
    .live-indicator .dot { width: 8px; height: 8px; background: #2c6ecb; border-radius: 50%; animation: pulse 2s infinite; }

    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(44, 110, 203, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(44, 110, 203, 0); } 100% { box-shadow: 0 0 0 0 rgba(44, 110, 203, 0); } }

    .builder-preview iframe { width: 100%; flex-grow: 1; border: none; }
    .property-group-title { padding: 16px 16px 0; font-size: 13px; font-weight: 600; color: #202223; border-top: 1px solid #e1e3e5; margin-top: 8px; }
</style>

<div class="editor-topbar">
    <div class="topbar-left">
        <a href="{{ route('ecommerce.admin.dashboard') }}" title="Exit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </a>
        <button class="icon-btn" title="Settings"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></button>
    </div>

    <div class="topbar-center">
        <select onchange="window.location.href = '?context=' + this.value">
            <option value="home" @selected($isHome)>Home page</option>
            <optgroup label="Custom Pages">
                @foreach($customPages as $cp)
                    <option value="{{$cp['slug']}}" @selected($context === $cp['slug'])>{{$cp['title']}}</option>
                @endforeach
            </optgroup>
        </select>
        <span class="context-badge">Active</span>
    </div>

    <div class="topbar-right">
        <button class="icon-btn" title="Desktop view" style="color: #202223;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></button>
        <button class="icon-btn" title="Mobile view"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></button>
        <div style="width: 1px; height: 24px; background: #e1e3e5; margin: 0 4px;"></div>
        <button class="icon-btn" title="Undo"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg></button>
        <button class="icon-btn" title="Redo"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg></button>
        <button class="icon-btn" title="More actions"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg></button>
        <button class="save-btn-top" style="background: #fff; color: #202223; border: 1px solid #c9cccf;" onclick="document.getElementById('layout-form').requestSubmit()">Save Draft</button>
        <form action="{{ route('ecommerce.admin.layout.publish') }}" method="POST" style="margin: 0;" onsubmit="if(confirm('Are you sure you want to publish your latest draft live to the storefront?')) { window.skipAutoSave = true; return true; } return false;">
            @csrf
            <button type="submit" class="save-btn-top">Publish Live</button>
        </form>
    </div>
</div>

<form id="layout-form" class="builder-container" method="post" enctype="multipart/form-data" action="{{ route('ecommerce.admin.layout.save') }}?context={{ $context }}">
    @csrf @method('put')
    <input id="section-order" type="hidden" name="section_order" value="{{ old('section_order', $order) }}">

    <!-- Left Sidebar (Main Tree) -->
    <div class="builder-sidebar" id="panel-main">
        <div class="sidebar-header">
            {{ $isHome ? 'Home page' : ($currentPage['title'] ?? 'Custom Page') }}
        </div>

        <div class="sidebar-group-title">Header</div>

        <!-- Header Section -->
        <div class="nav-item-wrapper" id="wrapper-header">
            <div class="nav-item nav-trigger" data-target="panel-header-main" onclick="openRightPanel('wrapper-header', 'panel-header-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-header')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
                    Header
                </div>
            </div>
            <div class="sub-items">

                <div class="sub-item" onclick="openRightPanel('wrapper-header', 'panel-header-storename'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg> Store name
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-header', 'panel-header-tagline'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg> Tagline
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-header', 'panel-header-colors'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg> Colors
                </div>
            </div>
        </div>



        <hr style="border: 0; border-top: 1px solid #e1e3e5; margin: 16px 0 0;">

        <div class="sidebar-group-title">Template</div>
        @if($isHome)
        <div id="sortable-sections">
        <!-- Hero Section -->
        <div class="nav-item-wrapper" id="wrapper-hero" draggable="true" data-section-id="hero">
            <div class="nav-item nav-trigger" data-target="panel-hero-main" onclick="openRightPanel('wrapper-hero', 'panel-hero-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-hero')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Hero
                </div>
            </div>
            <div class="sub-items">

                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-heading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Heading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-subheading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Subheading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-button'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg> Button
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-stats'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10M18 20V4M6 20v-4"/></svg> Stats Row
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-image'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Main Image
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-hero', 'panel-hero-marquee'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 5H19V11M19 5L5 19"/></svg> Features Marquee
                </div>
            </div>
        </div>

        <!-- Tiers Section -->
        <div class="nav-item-wrapper" id="wrapper-tiers" draggable="true" data-section-id="tiers">
            <div class="nav-item nav-trigger" data-target="panel-tiers-main" onclick="openRightPanel('wrapper-tiers', 'panel-tiers-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-tiers')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    Tiers
                </div>
            </div>
            <div class="sub-items">
                <div class="sub-item" onclick="openRightPanel('wrapper-tiers', 'panel-tiers-heading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Heading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-tiers', 'panel-tiers-subheading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Subheading
                </div>

                @php $tiers = $layout['sections'][array_search('tiers', array_column($layout['sections'], 'id'))] ?? []; @endphp
                @php $tiersBlocks = $tiers['blocks'] ?? []; @endphp
                <div id="tiers-blocks-container">
                    @foreach($tiersBlocks as $idx => $block)
                    <div class="sub-item tiers-block-nav" onclick="openRightPanel('wrapper-tiers', 'panel-tiers-block-{{ $idx }}'); highlightSub(this);" id="nav-tiers-block-{{ $idx }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg> <span>Item {{ $idx + 1 }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="sub-item add-section-btn" onclick="addBlock('tiers')" style="color:#2c6ecb; padding-left:32px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add item card
                </div>
            </div>
        </div>

        <!-- Prebuilts Section -->
        <div class="nav-item-wrapper" id="wrapper-prebuilts" draggable="true" data-section-id="prebuilts">
            <div class="nav-item nav-trigger" data-target="panel-prebuilts-main" onclick="openRightPanel('wrapper-prebuilts', 'panel-prebuilts-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-prebuilts')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    Prebuilts
                </div>
            </div>
            <div class="sub-items">
                <div class="sub-item" onclick="openRightPanel('wrapper-prebuilts', 'panel-prebuilts-heading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Heading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-prebuilts', 'panel-prebuilts-subheading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Subheading
                </div>

                @php $prebuilts = $layout['sections'][array_search('prebuilts', array_column($layout['sections'], 'id'))] ?? []; @endphp
                @php $prebuiltsBlocks = $prebuilts['blocks'] ?? []; @endphp
                <div id="prebuilts-blocks-container">
                    @foreach($prebuiltsBlocks as $idx => $block)
                    <div class="sub-item prebuilts-block-nav" onclick="openRightPanel('wrapper-prebuilts', 'panel-prebuilts-block-{{ $idx }}'); highlightSub(this);" id="nav-prebuilts-block-{{ $idx }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg> <span>Item {{ $idx + 1 }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="sub-item add-section-btn" onclick="addBlock('prebuilts')" style="color:#2c6ecb; padding-left:32px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add item card
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="nav-item-wrapper" id="wrapper-categories" draggable="true" data-section-id="categories">
            <div class="nav-item nav-trigger" data-target="panel-categories-main" onclick="openRightPanel('wrapper-categories', 'panel-categories-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-categories')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    Categories
                </div>
            </div>
            <div class="sub-items">
                <div class="sub-item" onclick="openRightPanel('wrapper-categories', 'panel-categories-heading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Heading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-categories', 'panel-categories-subheading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Subheading
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="nav-item-wrapper" id="wrapper-cta" draggable="true" data-section-id="cta">
            <div class="nav-item nav-trigger" data-target="panel-cta-main" onclick="openRightPanel('wrapper-cta', 'panel-cta-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-cta')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    CTA Banner
                </div>
            </div>
            <div class="sub-items">
                <div class="sub-item" onclick="openRightPanel('wrapper-cta', 'panel-cta-tag'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Tagline
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-cta', 'panel-cta-heading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Heading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-cta', 'panel-cta-subheading'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg> Subheading
                </div>
                <div class="sub-item" onclick="openRightPanel('wrapper-cta', 'panel-cta-buttons'); highlightSub(this);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg> Buttons
                </div>
            </div>
        </div>
        </div> <!-- End sortable-sections -->
        @endif



        <hr style="border: 0; border-top: 1px solid #e1e3e5; margin: 16px 0 0;">

        <div class="sidebar-group-title">Footer</div>
        <div class="nav-item-wrapper" id="wrapper-footer">
            <div class="nav-item nav-trigger" data-target="panel-footer-main" onclick="openRightPanel('wrapper-footer', 'panel-footer-main')">
                <div class="nav-item-left">
                    <span class="chevron-toggle" onclick="toggleExpand(event, 'wrapper-footer')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
                    Footer
                </div>
            </div>
        </div>
    </div>

    <!-- Center Preview Iframe -->
    <div class="builder-preview" id="preview-container">
        <div class="builder-preview-inner">
            <div class="preview-header">
                <div class="status">
                    <div class="live-indicator"><div class="dot"></div> Live Preview Active</div>
                </div>
            </div>
            <iframe id="preview-frame" src="{{ $previewUrl }}"></iframe>
        </div>
    </div>

    <!-- Right Sidebar (Properties Panels) -->
    <div class="builder-right-sidebar" id="right-sidebar">

        <!-- Header Panels -->
        <div class="right-panel-content" id="panel-header-main">
            <div class="panel-header">
                Header
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <p style="font-size: 13px; color: #5c5f62;">Select a block on the left to edit its properties.</p>
            </div>
        </div>
        <div class="right-panel-content" id="panel-header-storename">
            <div class="panel-header">
                Store name
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label>Store name<input name="brand_name" value="{{ old('brand_name', $layout['brand_name'] ?? '') }}" class="live-input"></label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-header-tagline">
            <div class="panel-header">
                Tagline
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label>Tagline<input name="tagline" value="{{ old('tagline', $layout['tagline'] ?? '') }}" class="live-input"></label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-header-colors">
            <div class="panel-header">
                Colors
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label>Primary color<input type="color" name="primary_color" value="{{ old('primary_color', $layout['primary_color'] ?? '#1d4e89') }}" style="height:32px; padding:0;" class="live-input"></label>
                <label>Accent color<input type="color" name="accent_color" value="{{ old('accent_color', $layout['accent_color'] ?? '#e83e8c') }}" style="height:32px; padding:0;" class="live-input"></label>
            </div>
        </div>

        <!-- Hero Panels -->
        <div class="right-panel-content" id="panel-hero-main">
            <div class="panel-header">
                Hero
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="hero_enabled" style="width:auto;" @checked(old('hero_enabled', $hero['enabled'] ?? false)) class="live-input"> Visible</label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-hero-heading">
            <div class="panel-header">
                Heading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'hero_title', 'label' => 'Headline', 'value' => old('hero_title', $hero['title'] ?? '')])
            </div>

            <div class="property-group-title">Layout</div>
            <div class="section-content" style="padding-top: 8px;">
                <label>Width
                    <select name="hero_title_width" class="live-input">
                        <option value="auto" @selected(old('hero_title_width', $hero['title_width'] ?? 'auto') === 'auto')>Auto</option>
                        <option value="full" @selected(old('hero_title_width', $hero['title_width'] ?? 'auto') === 'full')>Full Width</option>
                        <option value="narrow" @selected(old('hero_title_width', $hero['title_width'] ?? 'auto') === 'narrow')>Narrow</option>
                    </select>
                </label>
            </div>

            <div class="property-group-title">Typography</div>
            <div class="section-content" style="padding-top: 8px;">
                <label>Preset
                    <select name="hero_title_preset" class="live-input">
                        <option value="h1" @selected(old('hero_title_preset', $hero['title_preset'] ?? 'h1') === 'h1')>Heading 1</option>
                        <option value="h2" @selected(old('hero_title_preset', $hero['title_preset'] ?? 'h1') === 'h2')>Heading 2</option>
                        <option value="h3" @selected(old('hero_title_preset', $hero['title_preset'] ?? 'h1') === 'h3')>Heading 3</option>
                        <option value="body" @selected(old('hero_title_preset', $hero['title_preset'] ?? 'h1') === 'body')>Body text</option>
                    </select>
                </label>
            </div>

            <div class="property-group-title">Appearance</div>
            <div class="section-content" style="padding-top: 8px;">
                <label>Text Color<input type="color" name="hero_title_color" value="{{ old('hero_title_color', $hero['title_color'] ?? '#ffffff') }}" style="height:32px; padding:0;" class="live-input"></label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-hero-subheading">
            <div class="panel-header">
                Subheading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'hero_body', 'label' => 'Description', 'value' => old('hero_body', $hero['body'] ?? '')])
            </div>
        </div>

        <div class="right-panel-content" id="panel-hero-button">
            <div class="panel-header">
                Button
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @php $heroBtn = $hero['buttons'][0] ?? ['label' => '', 'url' => '#products']; @endphp
                <label>Button Label<input name="hero_buttons[0][label]" value="{{ old('hero_buttons.0.label', $heroBtn['label'] ?? '') }}" class="live-input"></label>
                <label>Button URL<input name="hero_buttons[0][url]" value="{{ old('hero_buttons.0.url', $heroBtn['url'] ?? '') }}" class="live-input"></label>
                <label>CTA Subtext<input name="hero_cta_subtext" value="{{ old('hero_cta_subtext', $hero['cta_subtext'] ?? '') }}" class="live-input"></label>
            </div>
        </div>

        <div class="right-panel-content" id="panel-hero-stats">
            <div class="panel-header">
                Stats Row
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @for($i = 0; $i < 3; $i++)
                <div style="margin-bottom: 12px; padding: 12px; border: 1px solid #e1e3e5; border-radius: 4px;">
                    <div style="font-size: 12px; font-weight: 600; margin-bottom: 8px;">Stat {{ $i + 1 }}</div>
                    <label>Value (e.g. 4,200+)<input name="hero_stats[{{ $i }}][value]" value="{{ old('hero_stats.'.$i.'.value', $hero['hero_stats'][$i]['value'] ?? '') }}" class="live-input" style="margin-bottom: 8px;"></label>
                    <label>Label (e.g. Units Shipped)<input name="hero_stats[{{ $i }}][label]" value="{{ old('hero_stats.'.$i.'.label', $hero['hero_stats'][$i]['label'] ?? '') }}" class="live-input"></label>
                </div>
                @endfor
            </div>
        </div>

        <div class="right-panel-content" id="panel-hero-image">
            <div class="panel-header">
                Main Image &amp; Gallery
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>

            <div class="property-group-title">Featured Listings</div>
            <div class="section-content" style="padding-top: 8px;">
                <p style="font-size: 12px; color: #5c5f62; margin-bottom: 12px;">Select up to 4 listings to display as gallery thumbnails. The first item's image appears as the main hero image.</p>
                @php $featuredConfigs = $hero['featured_configs'] ?? []; @endphp
                @for ($i = 0; $i < 4; $i++)
                <label>Featured Item {{ $i + 1 }}
                    <select name="hero_featured_configs[{{ $i }}]" class="live-input" style="margin-bottom: 8px;">
                        <option value="">â€” None â€”</option>
                        @foreach($availableListings as $listing)
                            <option value="{{ $listing->id }}" @selected(isset($featuredConfigs[$i]) && (string)$featuredConfigs[$i] === (string)$listing->id)>{{ $listing->name }}</option>
                        @endforeach
                    </select>
                </label>
                @endfor

                <label>Badge Text<input type="text" name="hero_badge_text" value="{{ old('hero_badge_text', $hero['badge_text'] ?? 'FEATURED BUILD') }}" class="live-input" placeholder="FEATURED BUILD"></label>
                <label>Gallery Cycle Speed (seconds)<input type="number" name="hero_gallery_cycle" value="{{ old('hero_gallery_cycle', $hero['gallery_cycle'] ?? 5) }}" class="live-input" min="1" max="60"></label>
            </div>

            <div class="property-group-title">Appearance</div>
            <div class="section-content" style="padding-top: 8px;">
                <label>Overlay Opacity (%)<input type="number" name="hero_overlay_opacity" value="{{ old('hero_overlay_opacity', $hero['overlay_opacity'] ?? 0) }}" class="live-input" min="0" max="100"></label>
            </div>
        </div>

        <div class="right-panel-content" id="panel-hero-marquee">
            <div class="panel-header">
                Features Marquee
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <p style="font-size: 13px; color: #5c5f62; margin-bottom: 16px;">Edit the scrolling text items at the bottom of the hero section.</p>
                @for($i = 0; $i < 6; $i++)
                <label>Item {{ $i + 1 }}<input name="hero_marquee[{{ $i }}][text]" value="{{ old('hero_marquee.'.$i.'.text', $hero['hero_marquee'][$i]['text'] ?? '') }}" class="live-input" style="margin-bottom: 8px;"></label>
                @endfor
            </div>
        </div>

        <!-- Tiers Panels -->
        <div class="right-panel-content" id="panel-tiers-main">
            <div class="panel-header">
                Tiers
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @php $tiers = $layout['sections'][array_search('tiers', array_column($layout['sections'], 'id'))] ?? []; @endphp
                <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="tiers_enabled" style="width:auto;" @checked(old('tiers_enabled', $tiers['enabled'] ?? false)) class="live-input"> Visible</label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-tiers-heading">
            <div class="panel-header">
                Heading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'tiers_title', 'label' => 'Text', 'value' => old('tiers_title', $tiers['title'] ?? "Select\nYour Tier")])
            </div>
        </div>
        <div class="right-panel-content" id="panel-tiers-subheading">
            <div class="panel-header">
                Subheading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'tiers_body', 'label' => 'Description', 'value' => old('tiers_body', $tiers['body'] ?? 'Four configurations. Every one tested under load for 72 hours before it leaves our facility.')])
            </div>
        </div>
        <div id="tiers-blocks-panels">
            @foreach($tiersBlocks as $idx => $block)
            <div class="right-panel-content tiers-block-panel" id="panel-tiers-block-{{ $idx }}">
                <div class="panel-header">
                    <span>Item {{ $idx + 1 }}</span>
                    <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>
                <div class="section-content">
                    <label>Select Listing
                        <select name="tiers_blocks[{{ $idx }}][listing_id]" class="live-input">
                            <option value="">-- Select Listing --</option>
                            @foreach($availableListings as $listing)
                                <option value="{{ $listing->id }}" @selected(($block['listing_id'] ?? '') == $listing->id)>{{ $listing->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Description
                        <textarea name="tiers_blocks[{{ $idx }}][description]" class="live-input" rows="4" placeholder="Override the listing's description for this cardâ€¦" style="resize:vertical;">{{ old('tiers_blocks.'.$idx.'.description', $block['description'] ?? '') }}</textarea>
                    </label>
                    <p style="font-size: 11px; color: #5c5f62; margin-top: -8px;">Leave blank to use the listing's own description.</p>
                    <div style="display:flex; gap:8px; margin-top:16px;">
                        <button type="button" onclick="duplicateBlock('tiers', {{ $idx }})" style="flex:1; padding:8px; border:1px solid #e1e3e5; color:#202223; background:transparent; border-radius:4px; cursor:pointer;">Duplicate</button>
                        @if($idx >= 4)
                        <button type="button" onclick="removeBlock('tiers', {{ $idx }})" style="flex:1; padding:8px; border:1px solid #ff4d4d; color:#ff4d4d; background:transparent; border-radius:4px; cursor:pointer;">Remove</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Prebuilts Panels -->
        <div class="right-panel-content" id="panel-prebuilts-main">
            <div class="panel-header">
                Prebuilts
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @php $prebuilts = $layout['sections'][array_search('prebuilts', array_column($layout['sections'], 'id'))] ?? []; @endphp
                <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="prebuilts_enabled" style="width:auto;" @checked(old('prebuilts_enabled', $prebuilts['enabled'] ?? false)) class="live-input"> Visible</label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-prebuilts-heading">
            <div class="panel-header">
                Heading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'prebuilts_title', 'label' => 'Text', 'value' => old('prebuilts_title', $prebuilts['title'] ?? "Pre-Built\nSystems")])
            </div>
        </div>
        <div class="right-panel-content" id="panel-prebuilts-subheading">
            <div class="panel-header">
                Subheading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'prebuilts_body', 'label' => 'Description', 'value' => old('prebuilts_body', $prebuilts['body'] ?? 'Ready to ship. Professionally assembled and stress-tested for out-of-the-box performance.')])
            </div>
        </div>
        <div id="prebuilts-blocks-panels">
            @foreach($prebuiltsBlocks as $idx => $block)
            <div class="right-panel-content prebuilts-block-panel" id="panel-prebuilts-block-{{ $idx }}">
                <div class="panel-header">
                    <span>Item {{ $idx + 1 }}</span>
                    <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                </div>
                <div class="section-content">
                    <label>Select Listing
                        <select name="prebuilts_blocks[{{ $idx }}][listing_id]" class="live-input">
                            <option value="">-- Select Listing --</option>
                            @foreach($availableListings as $listing)
                                <option value="{{ $listing->id }}" @selected(($block['listing_id'] ?? '') == $listing->id)>{{ $listing->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Description
                        <textarea name="prebuilts_blocks[{{ $idx }}][description]" class="live-input" rows="4" placeholder="Override the listing's description for this cardâ€¦" style="resize:vertical;">{{ old('prebuilts_blocks.'.$idx.'.description', $block['description'] ?? '') }}</textarea>
                    </label>
                    <p style="font-size: 11px; color: #5c5f62; margin-top: -8px;">Leave blank to use the listing's own description.</p>
                    <div style="display:flex; gap:8px; margin-top:16px;">
                        <button type="button" onclick="duplicateBlock('prebuilts', {{ $idx }})" style="flex:1; padding:8px; border:1px solid #e1e3e5; color:#202223; background:transparent; border-radius:4px; cursor:pointer;">Duplicate</button>
                        @if($idx >= 4)
                        <button type="button" onclick="removeBlock('prebuilts', {{ $idx }})" style="flex:1; padding:8px; border:1px solid #ff4d4d; color:#ff4d4d; background:transparent; border-radius:4px; cursor:pointer;">Remove</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Categories Panels -->
        <div class="right-panel-content" id="panel-categories-main">
            <div class="panel-header">
                Categories
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @php $categories = $layout['sections'][array_search('categories', array_column($layout['sections'], 'id'))] ?? []; @endphp
                <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="categories_enabled" style="width:auto;" @checked(old('categories_enabled', $categories['enabled'] ?? false)) class="live-input"> Visible</label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-categories-heading">
            <div class="panel-header">
                Heading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'categories_title', 'label' => 'Text', 'value' => old('categories_title', $categories['title'] ?? "Explore\nCategories")])
            </div>
        </div>
        <div class="right-panel-content" id="panel-categories-subheading">
            <div class="panel-header">
                Subheading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'categories_body', 'label' => 'Description', 'value' => old('categories_body', $categories['body'] ?? 'Find exactly what you need. From ready-to-ship systems to fully custom workstations.')])
            </div>
        </div>

        <!-- CTA Panels -->
        <div class="right-panel-content" id="panel-cta-main">
            <div class="panel-header">
                CTA Banner
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @php $cta = $layout['sections'][array_search('cta', array_column($layout['sections'], 'id'))] ?? []; @endphp
                <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="cta_enabled" style="width:auto;" @checked(old('cta_enabled', $cta['enabled'] ?? false)) class="live-input"> Visible</label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-cta-tag">
            <div class="panel-header">
                Tagline
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label>Tag text<input name="cta_tag_text" value="{{ old('cta_tag_text', $cta['tag_text'] ?? 'READY_TO_BUILD') }}" class="live-input"></label>
            </div>
        </div>
        <div class="right-panel-content" id="panel-cta-heading">
            <div class="panel-header">
                Heading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'cta_title', 'label' => 'Title', 'value' => old('cta_title', $cta['title'] ?? 'Stop Settling.')])
                @include('ecommerce::components.admin.rich-text', ['name' => 'cta_subtitle', 'label' => 'Subtitle', 'value' => old('cta_subtitle', $cta['subtitle'] ?? 'Start Winning.')])
            </div>
        </div>
        <div class="right-panel-content" id="panel-cta-subheading">
            <div class="panel-header">
                Subheading
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                @include('ecommerce::components.admin.rich-text', ['name' => 'cta_body', 'label' => 'Description', 'value' => old('cta_body', $cta['body'] ?? 'Free shipping. Free setup support. 30-day no-questions return policy. Your next machine is three clicks away.')])
            </div>
        </div>
        <div class="right-panel-content" id="panel-cta-buttons">
            <div class="panel-header">
                Buttons
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <label>Primary Button Label<input name="cta_primary_button_label" value="{{ old('cta_primary_button_label', $cta['primary_button_label'] ?? 'Build Yours Now') }}" class="live-input"></label>
                <label>Primary Button URL<input name="cta_primary_button_url" value="{{ old('cta_primary_button_url', $cta['primary_button_url'] ?? '/configurator') }}" class="live-input"></label>
                <div style="margin-top: 16px;"></div>
                <label>Secondary Button Label<input name="cta_secondary_button_label" value="{{ old('cta_secondary_button_label', $cta['secondary_button_label'] ?? 'Talk To An Expert') }}" class="live-input"></label>
                <label>Secondary Button URL<input name="cta_secondary_button_url" value="{{ old('cta_secondary_button_url', $cta['secondary_button_url'] ?? '/contact') }}" class="live-input"></label>
            </div>
        </div>

        <!-- Footer Panels -->
        <div class="right-panel-content" id="panel-footer-main">
            <div class="panel-header">
                Footer
                <button type="button" onclick="closeRightPanel()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="section-content">
                <p style="font-size: 13px; color: #5c5f62;">Footer settings</p>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleExpand(event, wrapperId) {
        event.stopPropagation();
        document.getElementById(wrapperId).classList.toggle('expanded');
    }

    function addBlock(section, duplicateFromIdx = null) {
        const container = document.getElementById(`${section}-blocks-container`);
        const panelsContainer = document.getElementById(`${section}-blocks-panels`);

        // Count existing blocks
        const blocks = container.querySelectorAll(`.${section}-block-nav`);
        const newIdx = blocks.length;

        // Create nav item
        const navItem = document.createElement('div');
        navItem.className = `sub-item ${section}-block-nav`;
        navItem.id = `nav-${section}-block-${newIdx}`;
        navItem.setAttribute('onclick', `openRightPanel('wrapper-${section}', 'panel-${section}-block-${newIdx}'); highlightSub(this);`);
        navItem.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg> <span>Item ${newIdx + 1}</span>`;

        container.appendChild(navItem);

        // Clone an existing panel to use as template
        let templatePanel = panelsContainer.querySelector('.right-panel-content');
        if (!templatePanel) return; // Should at least have one by default

        const newPanel = templatePanel.cloneNode(true);
        newPanel.id = `panel-${section}-block-${newIdx}`;
        newPanel.classList.remove('active');

        // Update title and inputs inside new panel
        newPanel.querySelector('.panel-header span').innerText = `Item ${newIdx + 1}`;

        const select = newPanel.querySelector('select');
        if (select) {
            select.name = `${section}_blocks[${newIdx}][listing_id]`;
            // If duplicating, set value to the original index value
            if (duplicateFromIdx !== null) {
                const originalSelect = document.querySelector(`select[name="${section}_blocks[${duplicateFromIdx}][listing_id]"]`);
                if (originalSelect) {
                    select.value = originalSelect.value;
                }
            } else {
                select.value = '';
            }

            // Re-bind live input event
            select.addEventListener('input', () => {
                if (typeof updateStaticPreview === 'function') updateStaticPreview();
            });
            select.addEventListener('change', () => {
                if (typeof updateStaticPreview === 'function') updateStaticPreview();
            });
        }

        // Update buttons
        const duplicateBtn = newPanel.querySelector('button[onclick^="duplicateBlock"]');
        if (duplicateBtn) duplicateBtn.setAttribute('onclick', `duplicateBlock('${section}', ${newIdx})`);

        let removeBtn = newPanel.querySelector('button[onclick^="removeBlock"]');
        if (!removeBtn && newIdx >= 4) {
            // Create remove button if duplicating from a block that doesn't have it
            removeBtn = document.createElement('button');
            removeBtn.setAttribute('type', 'button');
            removeBtn.setAttribute('style', 'flex:1; padding:8px; border:1px solid #ff4d4d; color:#ff4d4d; background:transparent; border-radius:4px; cursor:pointer;');
            removeBtn.innerText = 'Remove';
            newPanel.querySelector('div[style*="display:flex"]').appendChild(removeBtn);
        }
        if (removeBtn) {
            if (newIdx < 4) {
                removeBtn.remove();
            } else {
                removeBtn.setAttribute('onclick', `removeBlock('${section}', ${newIdx})`);
            }
        }

        panelsContainer.appendChild(newPanel);

        // Open the newly created block
        navItem.click();
    }

    function duplicateBlock(section, idx) {
        addBlock(section, idx);
    }

    function removeBlock(section, idx) {
        if (idx < 4) return; // Disallow removing the first 4 blocks

        const navItem = document.getElementById(`nav-${section}-block-${idx}`);
        const panelItem = document.getElementById(`panel-${section}-block-${idx}`);

        if (navItem) navItem.remove();
        if (panelItem) panelItem.remove();

        // Re-index remaining blocks
        const container = document.getElementById(`${section}-blocks-container`);
        const panelsContainer = document.getElementById(`${section}-blocks-panels`);

        const blocks = container.querySelectorAll(`.${section}-block-nav`);
        const panels = panelsContainer.querySelectorAll('.right-panel-content');

        blocks.forEach((block, index) => {
            block.id = `nav-${section}-block-${index}`;
            block.setAttribute('onclick', `openRightPanel('wrapper-${section}', 'panel-${section}-block-${index}'); highlightSub(this);`);
            block.querySelector('span').innerText = `Item ${index + 1}`;
        });

        panels.forEach((panel, index) => {
            panel.id = `panel-${section}-block-${index}`;
            panel.querySelector('.panel-header span').innerText = `Item ${index + 1}`;

            const select = panel.querySelector('select');
            if (select) {
                select.name = `${section}_blocks[${index}][listing_id]`;
            }

            const duplicateBtn = panel.querySelector('button[onclick^="duplicateBlock"]');
            if (duplicateBtn) duplicateBtn.setAttribute('onclick', `duplicateBlock('${section}', ${index})`);

            const removeBtn = panel.querySelector('button[onclick^="removeBlock"]');
            if (removeBtn) {
                if (index < 4) {
                    removeBtn.remove();
                } else {
                    removeBtn.setAttribute('onclick', `removeBlock('${section}', ${index})`);
                }
            }
        });

        closeRightPanel();
    }

    function highlightSub(element) {
        document.querySelectorAll('.sub-item').forEach(el => el.classList.remove('active'));
        if (element) element.classList.add('active');
    }

    function openRightPanel(wrapperId, panelId) {
        // Expand the wrapper if not expanded
        const wrapper = document.getElementById(wrapperId);
        if (wrapper && !wrapper.classList.contains('expanded')) {
            wrapper.classList.add('expanded');
        }

        // Remove active sub-item highlights if clicking the parent
        // (the onclick of sub-item will override this if it was a sub-item click)
        if (typeof window.event !== 'undefined' && window.event && window.event.currentTarget && window.event.currentTarget.classList && window.event.currentTarget.classList.contains('nav-trigger')) {
            document.querySelectorAll('.sub-item').forEach(el => el.classList.remove('active'));
        }

        // Hide all right panel contents
        document.querySelectorAll('.right-panel-content').forEach(el => el.classList.remove('active'));

        // Show the target panel content
        const target = document.getElementById(panelId);
        if (target) {
            target.classList.add('active');
        }

        // Open the right sidebar
        document.getElementById('right-sidebar').classList.add('open');
        document.getElementById('preview-container').classList.add('panel-open');
        updateIframeScale();
    }

    function closeRightPanel() {
        document.querySelectorAll('.sub-item').forEach(el => el.classList.remove('active'));
        document.getElementById('right-sidebar').classList.remove('open');
        document.getElementById('preview-container').classList.remove('panel-open');
        updateIframeScale();
    }

    function updateIframeScale() {
        const preview = document.querySelector('.builder-preview');
        const inner = document.querySelector('.builder-preview-inner');
        const container = document.querySelector('.builder-container');

        // Calculate true width to avoid transition mid-state values
        const fullWidth = container.offsetWidth - 300 - 32; // Container minus left sidebar (300) minus horizontal padding (32)
        const targetHeight = container.offsetHeight - 32; // Container minus vertical padding (32)

        if (preview.classList.contains('panel-open')) {
            const newWidth = fullWidth - 320; // Minus right sidebar
            const scale = newWidth / fullWidth;

            inner.style.width = fullWidth + 'px';
            inner.style.height = (targetHeight / scale) + 'px';
            inner.style.transform = `scale(${scale})`;
            inner.style.flexGrow = '0';
            inner.style.flexShrink = '0';
        } else {
            inner.style.width = '100%';
            inner.style.height = '100%';
            inner.style.transform = 'scale(1)';
            inner.style.flexGrow = '1';
            inner.style.flexShrink = '1';
        }
    }

    window.addEventListener('resize', () => {
        // debounce resize
        clearTimeout(window.resizeTimer);
        window.resizeTimer = setTimeout(updateIframeScale, 100);
    });

    (() => {
        const form = document.getElementById('layout-form');
        const iframe = document.getElementById('preview-frame');

        let debounceTimer;

        // Static UI updating
        const updateStaticPreview = () => {
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                if (!doc) return;

                const formData = new FormData(form);

                // Hero
                const heroH1 = doc.querySelector('main[data-preview-section="hero"] h1');
                if (heroH1) {
                    let title = formData.get('hero_title') || '';
                    heroH1.innerHTML = title.replace(/\{(.*?)\}/g, '<span class="text-primary drop-shadow-glow">$1</span>');

                    const color = formData.get('hero_title_color');
                    if (color) {
                        heroH1.style.color = color;
                        heroH1.classList.remove('text-white');
                    }
                }

                const heroP = doc.querySelector('main[data-preview-section="hero"] p.text-gray-400');
                if (heroP) {
                    heroP.textContent = formData.get('hero_body') || '';
                }

                // Button Label
                const heroBtnLabel = doc.querySelector('main[data-preview-section="hero"] a.bg-primary');
                if (heroBtnLabel) {
                    const btnLabel = formData.get('hero_buttons[0][label]');
                    if (btnLabel !== null) {
                        heroBtnLabel.innerHTML = btnLabel + ' &rarr;';
                    }
                }

                // CTA Subtext
                const heroCtaSubtext = doc.querySelector('main[data-preview-section="hero"] p.text-gray-500.text-xs.font-semibold');
                if (heroCtaSubtext) {
                    const subtext = formData.get('hero_cta_subtext');
                    if (subtext !== null) {
                        heroCtaSubtext.textContent = subtext;
                    }
                }

                // Stats
                const statsBlock = doc.querySelector('[data-preview-block="panel-hero-stats"]');
                if (statsBlock) {
                    for (let i = 0; i < 3; i++) {
                        const statVal = formData.get(`hero_stats[${i}][value]`);
                        const statLab = formData.get(`hero_stats[${i}][label]`);
                        if (statsBlock.children[i]) {
                            const valEl = statsBlock.children[i].querySelector('.text-xl, .text-2xl');
                            const labEl = statsBlock.children[i].querySelector('.text-gray-500');
                            if (valEl && statVal !== null) valEl.innerHTML = statVal;
                            if (labEl && statLab !== null) labEl.textContent = statLab;
                        }
                    }
                }

                // Overlay Opacity
                const overlay = doc.querySelector('[data-preview-block="panel-hero-image"] .absolute.inset-0.bg-black');
                if (overlay) {
                    const opacity = formData.get('hero_overlay_opacity') || 0;
                    overlay.style.opacity = opacity / 100;
                }

                // Tiers
                const tiersH2 = doc.querySelector('section[data-preview-section="tiers"] h2');
                if (tiersH2) {
                    const title = formData.get('tiers_title') || '';
                    tiersH2.innerHTML = title;
                }
                const tiersP = doc.querySelector('section[data-preview-section="tiers"] p.text-gray-400');
                if (tiersP) {
                    tiersP.textContent = formData.get('tiers_body') || '';
                }
                // Tiers block descriptions
                doc.querySelectorAll('section[data-preview-section="tiers"] [data-preview-block]').forEach((card, idx) => {
                    const descVal = formData.get(`tiers_blocks[${idx}][description]`);
                    if (descVal !== null) {
                        const descEl = card.querySelector('p.text-gray-300');
                        if (descEl) descEl.textContent = descVal || descEl.textContent;
                    }
                });

                // Prebuilts
                const prebuiltsH2 = doc.querySelector('section[data-preview-section="prebuilts"] h2');
                if (prebuiltsH2) {
                    const title = formData.get('prebuilts_title') || '';
                    prebuiltsH2.innerHTML = title;
                }
                const prebuiltsP = doc.querySelector('section[data-preview-section="prebuilts"] p.text-gray-400');
                if (prebuiltsP) {
                    prebuiltsP.textContent = formData.get('prebuilts_body') || '';
                }
                // Prebuilts block descriptions
                doc.querySelectorAll('section[data-preview-section="prebuilts"] [data-preview-block]').forEach((card, idx) => {
                    const descVal = formData.get(`prebuilts_blocks[${idx}][description]`);
                    if (descVal !== null) {
                        const descEl = card.querySelector('p.text-gray-300');
                        if (descEl) descEl.textContent = descVal || descEl.textContent;
                    }
                });

                // Categories
                const categoriesH2 = doc.querySelector('section[data-preview-section="categories"] h2');
                if (categoriesH2) {
                    const title = formData.get('categories_title') || '';
                    categoriesH2.innerHTML = title.replace(/\n/g, '<br>');
                }
                const categoriesP = doc.querySelector('section[data-preview-section="categories"] p.text-gray-400');
                if (categoriesP) {
                    categoriesP.textContent = formData.get('categories_body') || '';
                }

                // CTA
                const ctaTag = doc.querySelector('#cta-tag');
                if (ctaTag) {
                    ctaTag.textContent = formData.get('cta_tag_text') || '';
                }
                const ctaTitle = doc.querySelector('section[data-preview-section="cta"] h2 span.text-white');
                if (ctaTitle) {
                    ctaTitle.textContent = formData.get('cta_title') || '';
                }
                const ctaSubtitle = doc.querySelector('section[data-preview-section="cta"] h2 span.text-primary');
                if (ctaSubtitle) {
                    ctaSubtitle.textContent = formData.get('cta_subtitle') || '';
                }
                const ctaP = doc.querySelector('section[data-preview-section="cta"] p.text-gray-400');
                if (ctaP) {
                    ctaP.textContent = formData.get('cta_body') || '';
                }
                const ctaBtnPrimary = doc.querySelector('section[data-preview-section="cta"] a.bg-primary');
                if (ctaBtnPrimary) {
                    const btnLabel = formData.get('cta_primary_button_label');
                    if (btnLabel !== null) ctaBtnPrimary.innerHTML = btnLabel + ' &rarr;';
                }
                const ctaBtnSecondary = doc.querySelector('section[data-preview-section="cta"] a.border-white\\/20');
                if (ctaBtnSecondary) {
                    const btnLabel = formData.get('cta_secondary_button_label');
                    if (btnLabel !== null) {
                        ctaBtnSecondary.textContent = btnLabel;
                        iframe.contentWindow.postMessage({ type: 'staticSync' }, '*');
                    }
                }
            } catch (e) {
                console.warn('Could not sync static preview', e);
            }
        };
        window.updateStaticPreview = updateStaticPreview;

        // Live Preview Logic
        const refreshPreview = async () => {
            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                if (response.ok) {
                    const url = new URL(iframe.src);
                    url.searchParams.set('t', Date.now());
                    iframe.src = url.toString();

                    showToast('Draft saved successfully!');
                } else {
                    const err = await response.json();
                    console.error("Save failed:", err);
                    showToast('Failed to save draft.', 'error');
                }
            } catch (err) {
                console.error("Live preview sync failed", err);
                showToast('Network error while saving.', 'error');
            }
        };
        window.refreshPreview = refreshPreview;

        // Auto-save on page refresh/unload
        window.skipAutoSave = false;
        window.addEventListener('beforeunload', (e) => {
            if (!window.skipAutoSave) {
                const formData = new FormData(form);
                navigator.sendBeacon(form.action, formData);
            }
        });

        // Attach listeners to all inputs for immediate reflection
        document.querySelectorAll('.live-input').forEach(input => {
            input.addEventListener('input', () => {
                updateStaticPreview(); // Immediate reflection
            });
            if (input.type === 'checkbox') {
                input.addEventListener('change', () => {
                    updateStaticPreview();
                });
            }
        });

        // Normal save button
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            refreshPreview();
        });

        // Listen for section selection from iframe
        window.addEventListener('message', (event) => {
            if (event.data && event.data.action === 'select_section') {
                const sectionId = event.data.section;
                let wrapperId = 'wrapper-' + sectionId;
                let panelId = 'panel-' + sectionId + '-main';

                if (document.getElementById(wrapperId)) {
                    openRightPanel(wrapperId, panelId);
                    const trigger = document.querySelector('#' + wrapperId + ' .nav-trigger');
                    if (trigger) {
                        trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            } else if (event.data && event.data.action === 'select_block') {
                const sectionId = event.data.section;
                const panelId = event.data.block;

                let wrapperId = 'wrapper-' + sectionId;

                if (document.getElementById(wrapperId)) {
                    openRightPanel(wrapperId, panelId);

                    // Highlight the specific block on the left panel
                    const subItem = document.querySelector(`.sub-item[onclick*="${panelId}"]`);
                    if (subItem) {
                        highlightSub(subItem);
                        subItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        });

        // Drag and drop ordering for sections
        const sortableList = document.getElementById('sortable-sections');
        if (sortableList) {
            let draggedItem = null;

            sortableList.addEventListener('dragstart', (e) => {
                draggedItem = e.target.closest('.nav-item-wrapper');
                if (draggedItem) {
                    draggedItem.style.opacity = '0.5';
                    e.dataTransfer.effectAllowed = 'move';
                }
            });

            sortableList.addEventListener('dragend', (e) => {
                if (draggedItem) {
                    draggedItem.style.opacity = '1';
                    draggedItem = null;
                }
            });

            sortableList.addEventListener('dragover', (e) => {
                e.preventDefault();
                const afterElement = getDragAfterElement(sortableList, e.clientY);
                const wrapper = e.target.closest('.nav-item-wrapper');
                if (draggedItem && wrapper && wrapper !== draggedItem) {
                    if (afterElement == null) {
                        sortableList.appendChild(draggedItem);
                    } else {
                        sortableList.insertBefore(draggedItem, afterElement);
                    }
                    updateSectionOrder();
                }
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.nav-item-wrapper:not([style*="opacity: 0.5"])')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            function updateSectionOrder() {
                const wrappers = sortableList.querySelectorAll('.nav-item-wrapper');
                const newOrder = Array.from(wrappers).map(w => w.getAttribute('data-section-id')).filter(Boolean).join(',');
                const orderInput = document.getElementById('section-order');
                if (orderInput.value !== newOrder) {
                    orderInput.value = newOrder;
                }
            }
        }

        // Toast Notification System
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none;';
        document.body.appendChild(toastContainer);

        window.showToast = function(message, type = 'success') {
            const toast = document.createElement('div');
            toast.style.cssText = `
                padding: 12px 24px;
                border-radius: 6px;
                color: #fff;
                font-weight: 500;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                background: ${type === 'success' ? '#008060' : '#d8000c'};
            `;
            toast.innerText = message;

            toastContainer.appendChild(toast);

            // Trigger reflow
            void toast.offsetWidth;
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        // Show flash messages on page load if any
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if($errors->any())
            showToast("{{ $errors->first() }}", 'error');
        @endif
    })();
</script>
@endsection
