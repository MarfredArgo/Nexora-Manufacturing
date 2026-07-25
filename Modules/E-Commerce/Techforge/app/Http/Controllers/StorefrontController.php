<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Models\Company;
use Modules\Ecommerce\Models\CustombuiltConfig;
use Modules\Ecommerce\Models\PrebuiltConfig;
use Modules\Ecommerce\Models\StorefrontLayout;
use Modules\Ecommerce\Models\StorefrontListing;

class StorefrontController extends Controller
{
    public function index()
    {
        /** @var Company $company */
        $company = request()->attributes->get('ecommerce_company');

        return view('ecommerce::storefront', [
            'company' => $company,
            'layout' => StorefrontLayout::publishedFor($company),
            'storefrontListings' => StorefrontListing::query()->where('status', 'active')->latest()->take(12)->get(),
            'prebuiltPcs' => PrebuiltConfig::query()->latest()->take(6)->get(),
            'customConfigs' => CustombuiltConfig::query()->latest()->take(4)->get(),
            'preview' => false,
        ]);
    }
}
