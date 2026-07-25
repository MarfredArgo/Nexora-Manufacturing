@php
    $storefrontCompany = request()->attributes->get('ecommerce_company');
    $store = $storefrontCompany?->ecommerce_slug ?: 'techforge';
@endphp
@extends('ecommerce::admin.layout', ['title' => 'Storefront Listings', 'heading' => 'Storefront Listings'])

@section('content')
<section class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px"><p class="muted">Each listing attaches an active, Manufacturing-managed BOM.</p><a class="button" href="{{ route('ecommerce.admin.listings.create') }}">+ Add listing</a></div>
    <table><thead><tr><th>SKU</th><th>Name</th><th>Status</th><th>Available</th><th>Price</th><th></th></tr></thead><tbody>
        @forelse ($listings as $listing)
            <tr><td>{{ $listing->sku }}</td><td>{{ $listing->name }}</td><td>{{ ucfirst($listing->status) }}</td><td>{{ $listing->available_quantity }}</td><td>&#8369;{{ number_format((float) $listing->price, 2) }}</td><td><a class="button alt" href="{{ route('ecommerce.admin.listings.edit', $listing) }}">Edit</a></td></tr>
        @empty
            <tr><td colspan="6" class="muted">No listings yet.</td></tr>
        @endforelse
    </tbody></table>
</section>
@endsection
