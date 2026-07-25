<?php

namespace Modules\Manufacturing\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManufacturingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use the web guard explicitly.  Module routes are also available to
        // HR employees through their session, but a signed-in ITSM root admin
        // must be able to use the temporary QA access switch.
        $user = $request->user('web') ?? Auth::guard('web')->user();

        if (config('nexora.root_admin_module_testing') && $user?->role === 'root_admin') {
            return $next($request);
        }

        if (! session('employee_logged_in') || ! session('employee_client_id')) {
            // Do not use a route-relative redirect here.  If a reverse proxy
            // ever treats this request as being under /manufacturing, a
            // relative Location header would incorrectly become
            // /manufacturing/admin/itsm/registration after login.
            return redirect()->away($request->getSchemeAndHttpHost().'/login')->withErrors([
                'username' => 'Sign in with your approved HR employee account to access Manufacturing.',
            ]);
        }

        return $next($request);
    }
}
