<?php

namespace Modules\Ecommerce\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Modules\Ecommerce\Support\EcommerceClientContext;
use Symfony\Component\HttpFoundation\Response;

class ResolveStorefrontClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = strtolower(trim((string) $request->route('store')));

        // The route owns this value, but validate it before using it as a
        // company identifier. It prevents a malformed hostname from ever
        // being treated as a storefront.
        abort_unless(preg_match('/^[a-z0-9][a-z0-9-]{0,62}$/', $store), 404);

        $company = Company::query()
            ->where('status', 'Active')
            ->where('ecommerce_slug', $store)
            ->first();

        abort_unless($company, 404);

        app(EcommerceClientContext::class)->setClientId((int) $company->id);
        $request->attributes->set('ecommerce_company', $company);

        // All route() and redirect()->route() calls issued while serving the
        // store retain its subdomain without every Blade view needing to pass
        // the {store} parameter manually.
        URL::defaults(['store' => $company->ecommerce_slug]);

        return $next($request);
    }
}
