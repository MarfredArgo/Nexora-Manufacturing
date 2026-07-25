@extends('procurement::layouts.app')

@section('title', 'Nexora ERP — Suppliers')

@section('content')
<section id="page-suppliers">
      <div class="page-head">
        <h1>Suppliers</h1>
        <p>8 registered suppliers</p>
      </div>

      <div class="panel">
        <div class="table-toolbar">
          <h2>Supplier Directory</h2>
          <div class="search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <input placeholder="Search suppliers..." oninput="filterTable('suppliers-table', this.value)">
          </div>
           <button class="toolbar-btn" onclick="toggleFilterPanel('supplier-filter-panel', this)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 5h18l-7 8v6l-4 2v-8L3 5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            Filter
          </button>
          <button class="toolbar-btn primary" onclick="openAddModal('supplier')">+ Add Supplier</button>
        </div>
        <div class="filter-panel hidden" id="supplier-filter-panel">
          <div class="filter-group">
            <label>Brand</label>
            <select id="supplier-filter-brand" onchange="applySupplierFilter()">
              <option value="">All Brands</option>
              <option value="electronics">Electronics</option>
              <option value="components">Components</option>
              <option value="storage">Storage</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Status</label>
            <select id="supplier-filter-status" onchange="applySupplierFilter()">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="filter-actions">
            <button class="btn-text" onclick="clearSupplierFilter()">Clear</button>
            <button class="btn-primary" onclick="applySupplierFilter()">Apply</button>
          </div>
        </div>

        <table class="data-table sortable-table" id="suppliers-table">
          <thead>
            <tr>
              <th class="sortable" data-key="name">SUPPLIER<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="brand">BRAND<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="contact">CONTACT PERSON<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th class="sortable" data-key="email">EMAIL<span class="sort-arrows"><svg viewBox="0 0 8 5"><path d="M4 0L8 5H0z" fill="currentColor"/></svg><svg viewBox="0 0 8 5"><path d="M4 5L0 0h8z" fill="currentColor"/></svg></span></th>
              <th>PHONE</th>
              <th>ADDRESS</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="suppliers-tbody">
            @if(isset($suppliers) && count($suppliers))
              @foreach($suppliers as $s)
                @php
                  $parts = preg_split('/\s+/', trim($s->name));
                  $initials = '';
                  if($parts){
                    $initials = strtoupper(substr($parts[0],0,1));
                    if(count($parts) > 1) $initials .= strtoupper(substr($parts[count($parts)-1],0,1));
                  }
                  $colorMap = ['GigaCore Components'=>'#22c55e','Global Tech Supply'=>'#0ea5e9','MegaStar Trading'=>'#f2994a','Primo Electronics'=>'#22c55e','Quantum Motherboards'=>'#7a5af0','Silverline PSU Ltd'=>'#eb5757','Silverline PSU Ltd.'=>'#eb5757','TechWholesale PH'=>'#2f6fed','Trident RAM Supply'=>'#0ea5e9'];
                  $colors = ['#2f6fed','#22c55e','#f2994a','#7a5af0','#eb5757','#0ea5e9','#1fa971','#e0338c'];
                  $badgeColor = $colorMap[$s->name] ?? null;
                  if(!$badgeColor){
                    $h = 0; foreach(str_split($s->name ?? '') as $ch) $h = ($h*31 + ord($ch)) & 0xffff;
                    $badgeColor = $colors[$h % count($colors)];
                  }
                @endphp
                <tr data-id="{{ $s->id }}" data-brand="{{ $s->brand ?? '' }}" data-warehouse-id="{{ $s->warehouse_id ?? '' }}" data-products='@json($s->product_items ? json_decode($s->product_items, true) : [])'>
                  <td><div class="supplier-pill-cell"><span class="supplier-pill"><span class="supplier-badge" style="background: {{ $badgeColor }}">{{ $initials }}</span>{{ $s->name }}</span></div></td>
                  <td>{{ $s->brand ?? '—' }}</td>
                  <td>{{ $s->contact_person ?? '—' }}</td>
                  <td>{{ $s->email ?? '—' }}</td>
                  <td>{{ $s->phone ?? '—' }}</td>
                  <td style="max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $s->address ?? '—' }}</td>
                  <td><span class="row-actions"><button title="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg></button><button title="Edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></button><button class="del" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button></span></td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="7" style="text-align:center; padding:32px 16px; color:var(--text-muted);">No suppliers yet.</td>
              </tr>
            @endif
          </tbody>
        </table>
        <div class="table-footer">
          <br>
          <div>Showing <b>{{ isset($suppliers) ? count($suppliers) : 0 }}</b> suppliers</div>
          <div class="pager"><button class="page-btn">‹</button><button class="page-btn active">1</button><button class="page-btn">›</button></div>
        </div>
      </div>
    </section>
@endsection
