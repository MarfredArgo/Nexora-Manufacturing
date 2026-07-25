@php
    $storefrontCompany = request()->attributes->get('ecommerce_company');
    $store = $storefrontCompany?->ecommerce_slug ?: 'techforge';
@endphp
@extends('ecommerce::admin.layout', ['title' => 'E-commerce Overview', 'heading' => 'E-commerce Overview'])

@section('content')

<style>
    .welcome-header {
        text-align: center;
        margin: 40px 0 32px;
    }
    .welcome-header h1 {
        font-size: 24px;
        color: var(--c-text-muted);
        font-weight: 500;
        margin-bottom: 8px;
    }
    .welcome-header h2 {
        font-size: 32px;
        color: var(--c-text);
        font-weight: 600;
    }

    .onboarding-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 40px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }

    .onboard-card {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }
    .onboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .onboard-card h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--c-text);
    }
    .onboard-card p {
        font-size: 14px;
        color: var(--c-text-muted);
        margin-bottom: 24px;
        line-height: 1.5;
        flex: 1;
    }

    .onboard-visual {
        height: 140px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #d1d5db;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: 20px;
    }
    .stat-card .muted {
        font-size: 13px;
        color: var(--c-text-muted);
        font-weight: 500;
    }
    .stat-card .metric {
        font-size: 28px;
        font-weight: 700;
        margin-top: 8px;
        color: var(--c-text);
    }

    @media (max-width: 900px) {
        .onboarding-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="welcome-header">
    <h1>Welcome to Nexora!</h1>
    <h2>Where do you want to start?</h2>
</div>

<div class="onboarding-grid">
    <!-- Add Product Card -->
    <div class="onboard-card">
        <h3>Add your first product</h3>
        <p>Start with a title, price, and a photo. You can always add more detail later.</p>
        <div class="onboard-visual">
            <i class="ph ph-t-shirt"></i>
        </div>
        <div>
            <a href="{{ route('ecommerce.admin.listings.create') }}" class="button alt">Add product</a>
        </div>
    </div>

    <!-- Choose Theme Card -->
    <div class="onboard-card">
        <h3>Choose your store design</h3>
        <p>Pick a theme that fits your brand, then customize from there.</p>
        <div class="onboard-visual">
            <i class="ph ph-paint-brush"></i>
        </div>
        <div>
            <a href="{{ route('ecommerce.admin.layout.edit') }}" class="button alt">Choose theme</a>
        </div>
    </div>
</div>

<hr style="border: 0; border-top: 1px solid var(--c-border); margin: 48px 0;">

<div class="stats-grid">
    <div class="stat-card"><span class="muted">Available BOMs</span><div class="metric">{{ $bomCount }}</div></div>
    <div class="stat-card"><span class="muted">Total Listings</span><div class="metric">{{ $listingCount }}</div></div>
    <div class="stat-card"><span class="muted">Active Listings</span><div class="metric">{{ $activeListingCount }}</div></div>
    <div class="stat-card"><span class="muted">Total Orders</span><div class="metric">{{ $orderCount }}</div></div>
</div>

<section class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px; margin-bottom: 16px;">
        <h2 style="font-size: 18px; font-weight: 600;">Recent listings</h2>
        <span>
            <a class="button" style="padding: 6px 12px; font-size: 13px;" href="{{ route('ecommerce.admin.listings.create') }}">+ Add listing</a>
        </span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Available</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentListings as $listing)
                <tr>
                    <td><strong>{{ $listing->name ?: 'Item #'.$listing->inventory_item_id }}</strong></td>
                    <td><span style="display:inline-block; padding: 2px 8px; border-radius: 12px; background: {{ $listing->status === 'active' ? '#e8f5e9' : '#f5f5f5' }}; color: {{ $listing->status === 'active' ? '#2e7d32' : '#666' }}; font-size: 12px; font-weight: 500;">{{ $listing->status === 'active' ? 'Active' : 'Draft' }}</span></td>
                    <td>-</td>
                    <td>&#8369;{{ number_format((float) ($listing->override_price ?: 0), 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted" style="text-align: center; padding: 32px 0;">No storefront listings found. Start by adding a product above.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

@endsection
