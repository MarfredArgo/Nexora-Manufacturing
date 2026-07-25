<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\StorefrontLayout;
use Modules\Ecommerce\Support\EcommerceClientContext;

class DynamicPageController extends Controller
{
    public function show(string $slug, EcommerceClientContext $context)
    {
        $company = $context->company();
        if (!$company) {
            abort(404);
        }

        $isPreview = request()->boolean('preview') && \Illuminate\Support\Facades\Auth::guard('ecommerce_admin')->check();
        $layout = $isPreview ? StorefrontLayout::editableFor($company) : StorefrontLayout::publishedFor($company);

        $customPages = collect($layout['custom_pages'] ?? []);

        // Find the page by slug
        $page = $customPages->firstWhere('slug', $slug);

        if (!$page) {
            abort(404);
        }

        $blueprint = $page['blueprint'] ?? '';

        // Map blueprints to their corresponding controller logic
        return match ($blueprint) {
            'storefront' => app(StorefrontController::class)->index(),
            'cart' => app(CartController::class)->index(),
            'checkout' => app(CheckoutController::class)->index(),
            'pc-configurator' => app(\Modules\Ecommerce\Http\Controllers\CustomPcController::class)->index(),
            'gaming-laptops' => app(\Modules\Ecommerce\Http\Controllers\LaptopController::class)->index(),
            'prebuilt-pcs' => app(\Modules\Ecommerce\Http\Controllers\PrebuiltPcController::class)->index(),
            default => abort(404, "Blueprint '{$blueprint}' not found."),
        };
    }
}
