<?php

namespace Modules\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForgetStoreParameter
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->route()) {
            $request->route()->forgetParameter('store');
        }
        return $next($request);
    }
}
