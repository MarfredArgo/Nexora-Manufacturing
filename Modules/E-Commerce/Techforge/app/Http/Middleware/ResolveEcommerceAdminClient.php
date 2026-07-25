<?php

namespace Modules\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Ecommerce\Support\EcommerceClientContext;
use Symfony\Component\HttpFoundation\Response;

class ResolveEcommerceAdminClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('ecommerce_admin')->user();

        if (! $admin || ! $admin->isEcommerceEmployee()) {
            Auth::guard('ecommerce_admin')->logout();

            return redirect()->route('ecommerce.admin.login')
                ->withErrors(['email' => 'Your E-commerce account is not active.']);
        }

        app(EcommerceClientContext::class)->setClientId((int) $admin->client_id);

        return $next($request);
    }
}
