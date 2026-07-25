<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\Order;
use Modules\Ecommerce\Models\StorefrontLayout;
use Modules\Ecommerce\Models\StorefrontListing;
use Modules\Ecommerce\Support\EcommerceClientContext;

class EcommerceAdminController extends Controller
{
    public function login() { return view('ecommerce::admin.login'); }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::guard('ecommerce_admin')->attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->route('ecommerce.admin.dashboard');
    }

    public function dashboard()
    {
        $clientId = (int) app(EcommerceClientContext::class)->clientId();
        return view('ecommerce::admin.dashboard', [
            'listingCount' => StorefrontListing::count(),
            'activeListingCount' => StorefrontListing::where('status', 'active')->count(),
            'orderCount' => Order::count(),
            'bomCount' => DB::connection('manufacturing')->table('product_boms')->where('client_id', $clientId)->where('status', 'active')->count(),
            'recentListings' => StorefrontListing::latest()->take(5)->get(),
        ]);
    }

    public function listings() { return view('ecommerce::admin.listings', ['listings' => StorefrontListing::latest()->get()]); }
    public function createListing() { return view('ecommerce::admin.listing-form', ['listing' => new StorefrontListing(), 'boms' => $this->boms()]); }

    public function storeListing(Request $request): RedirectResponse
    {
        $data = $this->listingData($request);
        if ($request->hasFile('image')) $data['image_url'] = $request->file('image')->store('storefront-listings', 'public');
        StorefrontListing::create($data);
        return redirect()->route('ecommerce.admin.listings')->with('success', 'Storefront listing created.');
    }

    public function editListing(StorefrontListing $listing) { return view('ecommerce::admin.listing-form', ['listing' => $listing, 'boms' => $this->boms()]); }

    public function updateListing(Request $request, StorefrontListing $listing): RedirectResponse
    {
        $data = $this->listingData($request);
        if ($request->hasFile('image')) $data['image_url'] = $request->file('image')->store('storefront-listings', 'public');
        $listing->update($data);
        return redirect()->route('ecommerce.admin.listings')->with('success', 'Storefront listing updated.');
    }

    public function destroyListing(StorefrontListing $listing): RedirectResponse
    {
        $listing->delete();
        return redirect()->route('ecommerce.admin.listings')->with('success', 'Storefront listing removed.');
    }

    public function orders() { return view('ecommerce::admin.orders', ['orders' => Order::latest()->paginate(20)]); }

    public function editLayout(Request $request)
    {
        $company = $this->company();

        $context = $request->query('context', 'home');

        return view('ecommerce::admin.layout-editor', [
            'layout' => StorefrontLayout::editableFor($company),
            'hasPublishedLayout' => StorefrontLayout::query()->whereNotNull('published_layout')->exists(),
            'company' => $company,
            'availableConfigs' => [],
            'availableListings' => \Modules\Ecommerce\Models\StorefrontListing::orderBy('name')->get(),
            'context' => $context,
        ]);
    }

    public function saveLayout(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $company = $this->company();
        $record = StorefrontLayout::query()->first();
        $current = $record?->draft_layout ?: $record?->published_layout ?: StorefrontLayout::defaultFor($company);
        $layout = $this->layoutData($request, $current);

        if ($request->hasFile('logo')) {
            $layout['logo_path'] = $request->file('logo')->store('storefront-layouts', 'public');
        }

        if ($request->hasFile('hero_image')) {
            $layout['sections'] = collect($layout['sections'])->map(function (array $section) use ($request): array {
                if ($section['id'] === 'hero') {
                    $section['image_path'] = $request->file('hero_image')->store('storefront-layouts', 'public');
                }

                return $section;
            })->all();
        }

        if ($record) {
            $record->update(['draft_layout' => $layout]);
        } else {
            StorefrontLayout::create(['draft_layout' => $layout]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Saved successfully']);
        }

        return redirect()->route('ecommerce.admin.layout.edit')->with('success', 'Draft saved. Preview it, then publish when you are ready.');
    }

    public function previewLayout()
    {
        $company = $this->company();

        $layout = StorefrontLayout::editableFor($company);
        $hero = collect($layout['sections'] ?? [])->firstWhere('id', 'hero');
        $featuredConfigIds = array_values(array_filter($hero['featured_configs'] ?? []));

        if (!empty($featuredConfigIds)) {
            $customConfigs = StorefrontListing::whereIn('id', $featuredConfigIds)
                ->where('status', 'active')
                ->get()
                ->sortBy(fn($listing) => array_search((string) $listing->id, array_map('strval', $featuredConfigIds)))
                ->values();
        } else {
            $customConfigs = collect([]);
        }

        return view('ecommerce::storefront', [
            'company' => $company,
            'layout' => $layout,
            'storefrontListings' => StorefrontListing::query()->where('status', 'active')->latest()->take(12)->get(),
            'allListings' => StorefrontListing::query()->get()->keyBy('id'),
            'prebuiltPcs' => [],
            'customConfigs' => $customConfigs,
            'preview' => true,
        ]);
    }

    public function publishLayout(): RedirectResponse
    {
        $company = $this->company();
        $record = StorefrontLayout::query()->first();

        if (! $record?->draft_layout) {
            return redirect()->route('ecommerce.admin.layout.edit')->withErrors([
                'layout' => 'Save a layout draft before publishing it.',
            ]);
        }

        $record->update(['published_layout' => $record->draft_layout]);

        return redirect()->route('ecommerce.admin.layout.edit')->with('success', 'Your storefront layout is live on '.$company->ecommerce_slug.'.'.config('ecommerce.storefront_base_domain').'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('ecommerce_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('ecommerce.admin.login');
    }

    private function boms()
    {
        return DB::connection('manufacturing')->table('product_boms')->where('client_id', app(EcommerceClientContext::class)->clientId())->where('status', 'active')->orderBy('name')->get();
    }

    private function listingData(Request $request): array
    {
        return $request->validate([
            'bom_id' => ['required', 'integer'], 'sku' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:160'], 'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'], 'status' => ['required', 'in:draft,active,archived'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function company(): Company
    {
        $company = auth('ecommerce_admin')->user()?->getCompany();
        abort_unless($company instanceof Company && (int) $company->id === (int) app(EcommerceClientContext::class)->clientId(), 403);

        return $company;
    }

    private function layoutData(Request $request, array $current): array
    {
        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'section_order' => ['required', 'string', 'max:100'],
            'hero_title' => ['nullable', 'string', 'max:160'],
            'hero_title_preset' => ['nullable', 'string', 'in:h1,h2,h3,body'],
            'hero_title_width' => ['nullable', 'string', 'in:auto,full,narrow'],
            'hero_title_color' => ['nullable', 'string'],
            'hero_visual_style' => ['nullable', 'in:showcase,gallery'],
            'hero_badge_text' => ['nullable', 'string', 'max:50'],
            'hero_gallery_cycle' => ['nullable', 'integer', 'min:1', 'max:60'],
            'hero_featured_configs' => ['nullable', 'array', 'max:4'],
            'hero_featured_configs.*' => ['nullable', 'integer'],
            'hero_body' => ['nullable', 'string', 'max:600'],
            'hero_button_alignment' => ['nullable', 'in:start,center,end'],
            'hero_overlay_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hero_particles_count' => ['nullable', 'integer', 'min:10', 'max:200'],
            'hero_particles_speed' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
            'hero_cta_subtext' => ['nullable', 'string', 'max:150'],
            'hero_buttons' => ['nullable', 'array', 'max:5'],
            'hero_buttons.*.label' => ['nullable', 'string', 'max:60'],
            'hero_buttons.*.url' => ['nullable', 'string', 'max:255'],
            'hero_buttons.*.style' => ['nullable', 'in:primary,secondary'],
            'hero_stats' => ['nullable', 'array', 'max:3'],
            'hero_stats.*.value' => ['nullable', 'string', 'max:20'],
            'hero_stats.*.label' => ['nullable', 'string', 'max:50'],
            'hero_marquee' => ['nullable', 'array', 'max:10'],
            'hero_marquee.*.text' => ['nullable', 'string', 'max:100'],
            'listings_title' => ['nullable', 'string', 'max:100'],
            'listings_body' => ['nullable', 'string', 'max:300'],
            'tiers_title' => ['nullable', 'string', 'max:140'],
            'tiers_body' => ['nullable', 'string', 'max:600'],
            'tiers_blocks' => ['nullable', 'array', 'max:20'],
            'tiers_blocks.*.listing_id' => ['nullable', 'string', 'max:36'],
            'tiers_blocks.*.description' => ['nullable', 'string', 'max:500'],
            'prebuilts_title' => ['nullable', 'string', 'max:140'],
            'prebuilts_body' => ['nullable', 'string', 'max:600'],
            'prebuilts_blocks' => ['nullable', 'array', 'max:20'],
            'prebuilts_blocks.*.listing_id' => ['nullable', 'string', 'max:36'],
            'prebuilts_blocks.*.description' => ['nullable', 'string', 'max:500'],
            'categories_title' => ['nullable', 'string', 'max:140'],
            'categories_body' => ['nullable', 'string', 'max:600'],
            'cta_title' => ['nullable', 'string', 'max:140'],
            'cta_subtitle' => ['nullable', 'string', 'max:140'],
            'cta_body' => ['nullable', 'string', 'max:600'],
            'cta_primary_button_label' => ['nullable', 'string', 'max:60'],
            'cta_primary_button_url' => ['nullable', 'string', 'max:255'],
            'cta_secondary_button_label' => ['nullable', 'string', 'max:60'],
            'cta_secondary_button_url' => ['nullable', 'string', 'max:255'],
            'cta_tag_text' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
            'announcement_text' => ['nullable', 'string', 'max:100'],
            'announcement_url' => ['nullable', 'string', 'max:255'],
            'search_placeholder' => ['nullable', 'string', 'max:50'],
            'trending_searches' => ['nullable', 'string', 'max:255'],
                        'nav_links' => ['nullable', 'array', 'max:10'],
            'nav_links.*.label' => ['nullable', 'string', 'max:50'],
            'nav_links.*.url' => ['nullable', 'string', 'max:255'],
            'nav_links.*.type' => ['nullable', 'in:simple,mega'],
            'nav_links.*.promo_title' => ['nullable', 'string', 'max:100'],
            'nav_links.*.promo_subtitle' => ['nullable', 'string', 'max:200'],
            'nav_links.*.promo_button' => ['nullable', 'string', 'max:60'],
            'nav_links.*.promo_button_url' => ['nullable', 'string', 'max:255'],
            'custom_pages' => ['nullable', 'array', 'max:50'],
            'custom_pages.*.id' => ['required_with:custom_pages', 'string'],
            'custom_pages.*.title' => ['required_with:custom_pages', 'string', 'max:160'],
            'custom_pages.*.slug' => ['required_with:custom_pages', 'string', 'max:100'],
            'custom_pages.*.blueprint' => ['required_with:custom_pages', 'string', 'max:50'],
        ]);

        $context = $request->query('context', 'home');
        $allowedSections = ['hero', 'tiers', 'prebuilts', 'categories', 'cta'];

        if ($context === 'home') {
            $order = array_values(array_unique(array_filter(explode(',', $validated['section_order'] ?? ''), fn (string $id): bool => in_array($id, $allowedSections, true))));
            $order = array_values(array_unique([...$order, ...$allowedSections]));
            $existing = collect($current['sections'] ?? [])->keyBy('id');
            $section = fn (string $id): array => (array) $existing->get($id, ['id' => $id]);

            $hero = $section('hero');
            $heroButtons = collect($validated['hero_buttons'] ?? [])->map(fn($btn) => [
                'label' => $btn['label'],
                'url' => $this->safeStorefrontLink($btn['url'] ?? '#products'),
                'style' => $btn['style'] ?? 'primary'
            ])->all();
            $hero = [
                ...$hero,
                'id' => 'hero',
                'enabled' => $request->boolean('hero_enabled'),
                'title' => $validated['hero_title'] ?: 'Products built for your {next big move}.',
                'title_preset' => $validated['hero_title_preset'] ?? 'h1',
                'title_width' => $validated['hero_title_width'] ?? 'auto',
                'title_color' => $validated['hero_title_color'] ?? '#ffffff',
                'body' => $validated['hero_body'] ?: '',
                'button_alignment' => $validated['hero_button_alignment'] ?? 'start',
                'buttons' => $heroButtons,
                'cta_subtext' => $validated['hero_cta_subtext'] ?? '',
                'visual_style' => $validated['hero_visual_style'] ?? 'showcase',
                'badge_text' => $validated['hero_badge_text'] ?? 'FEATURED BUILD',
                'gallery_cycle' => $validated['hero_gallery_cycle'] ?? 5,
                'featured_configs' => array_values(array_filter($validated['hero_featured_configs'] ?? [])),
                'overlay_opacity' => $validated['hero_overlay_opacity'] ?? 0,
                'hero_stats' => $validated['hero_stats'] ?? [],
                'hero_marquee' => $validated['hero_marquee'] ?? [],
                'particles_enabled' => $request->boolean('hero_particles_enabled'),
                'particles_count' => $validated['hero_particles_count'] ?? 40,
                'particles_speed' => $validated['hero_particles_speed'] ?? 1.0
            ];

            $tiers = $section('tiers');
            $tiers = [...$tiers, 'id' => 'tiers', 'enabled' => $request->boolean('tiers_enabled'), 'title' => $validated['tiers_title'] ?: "Select\nYour Tier", 'body' => $validated['tiers_body'] ?: 'Four configurations. Every one tested under load for 72 hours before it leaves our facility.', 'blocks' => array_values($validated['tiers_blocks'] ?? [])];

            $prebuilts = $section('prebuilts');
            $prebuilts = [...$prebuilts, 'id' => 'prebuilts', 'enabled' => $request->boolean('prebuilts_enabled'), 'title' => $validated['prebuilts_title'] ?: "Pre-Built\nSystems", 'body' => $validated['prebuilts_body'] ?: 'Ready to ship. Professionally assembled and stress-tested for out-of-the-box performance.', 'blocks' => array_values($validated['prebuilts_blocks'] ?? [])];

            $categories = $section('categories');
            $categories = [...$categories, 'id' => 'categories', 'enabled' => $request->boolean('categories_enabled'), 'title' => $validated['categories_title'] ?: "Explore\nCategories", 'body' => $validated['categories_body'] ?: 'Find exactly what you need. From ready-to-ship systems to fully custom workstations.'];

            $cta = $section('cta');
            $cta = [...$cta, 'id' => 'cta', 'enabled' => $request->boolean('cta_enabled'), 'title' => $validated['cta_title'] ?: "Stop Settling.", 'subtitle' => $validated['cta_subtitle'] ?: "Start Winning.", 'body' => $validated['cta_body'] ?: 'Free shipping. Free setup support. 30-day no-questions return policy. Your next machine is three clicks away.', 'primary_button_label' => $validated['cta_primary_button_label'] ?: 'Build Yours Now', 'primary_button_url' => $this->safeStorefrontLink($validated['cta_primary_button_url'] ?? '/configurator'), 'secondary_button_label' => $validated['cta_secondary_button_label'] ?: 'Talk To An Expert', 'secondary_button_url' => $this->safeStorefrontLink($validated['cta_secondary_button_url'] ?? '/contact'), 'tag_text' => $validated['cta_tag_text'] ?: 'READY_TO_BUILD'];

            $sectionsById = [
                'hero' => $hero,
                'tiers' => $tiers,
                'prebuilts' => $prebuilts,
                'categories' => $categories,
                'cta' => $cta,
            ];

            $sections = array_map(fn (string $id): array => $sectionsById[$id], $order);
        } else {
            $sections = $current['sections'] ?? [];
        }

        return [
            'brand_name' => $validated['brand_name'] ?? $current['brand_name'] ?? 'Nexora',
            'tagline' => $validated['tagline'] ?: 'Official Nexora storefront',
            'primary_color' => $validated['primary_color'] ?? $current['primary_color'] ?? '#ff6b00',
            'accent_color' => $validated['accent_color'] ?? $current['accent_color'] ?? '#f59e0b',
            'logo_path' => $current['logo_path'] ?? null,
            'custom_pages' => $validated['custom_pages'] ?? $current['custom_pages'] ?? [],
            'sections' => $sections,
            'navbar' => [
                'announcement_enabled' => $request->has('announcement_enabled') ? $request->boolean('announcement_enabled') : ($current['navbar']['announcement_enabled'] ?? true),
                'announcement_text' => $validated['announcement_text'] ?? $current['navbar']['announcement_text'] ?? 'ðŸ”¥ Free shipping on all orders over â‚±50,000!',
                'announcement_url' => $validated['announcement_url'] ?? $current['navbar']['announcement_url'] ?? '',
                'search_placeholder' => $validated['search_placeholder'] ?? $current['navbar']['search_placeholder'] ?? 'What are we searching?',
                'trending_searches' => $validated['trending_searches'] ?? $current['navbar']['trending_searches'] ?? 'RTX 4090, Ryzen 7 7800X3D, Prebuilt Gaming PC, 32GB DDR5 RAM',
                'links' => $validated['nav_links'] ?? $current['navbar']['links'] ?? [],
            ],
        ];
    }

    private function safeStorefrontLink(?string $url): string
    {
        $url = trim((string) $url);

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL)
            ? $url
            : '#products';
    }
}
