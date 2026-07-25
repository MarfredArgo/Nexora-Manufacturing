@php
    $admin = auth('ecommerce_admin')->user();
    $company = $admin?->getCompany();
    $logoUrl = $company?->logoUrl();
    $companyName = $company?->company_name ?? 'Admin Panel';
@endphp

@if ($logoUrl)
    <img
        src="{{ $logoUrl }}"
        alt="{{ $companyName }}"
        style="max-height: 2rem; max-width: 10rem; object-fit: contain;"
    />
@else
    <span style="font-weight: 700; font-size: 1.1rem; letter-spacing: 0.01em;">
        {{ $companyName }}
    </span>
@endif
