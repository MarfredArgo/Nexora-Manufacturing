<?php

namespace App\Http\Middleware;

use App\Services\ErpIntegrationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditModuleAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $clientId = (int) (session('employee_client_id') ?: $request->attributes->get('ecommerce_company')?->id ?: $request->user()?->company_id);
        $department = $this->department($request);
        $actor = session('employee_name') ?: $request->user()?->username ?: $request->user()?->email;
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) || $response->getStatusCode() >= 400) {
            return $response;
        }

        if ($clientId <= 0) {
            return $response;
        }

        app(ErpIntegrationService::class)->recordAudit($clientId, 'action.'.strtolower($request->method()), $department, [
            'route' => $request->route()?->getName(),
            'path' => '/'.$request->path(),
            'actor' => $actor,
        ]);

        return $response;
    }

    private function department(Request $request): string
    {
        $route = (string) $request->route()?->getName();
        foreach (['hr' => 'Human Resources', 'inventory' => 'Inventory', 'procurement' => 'Procurement', 'manufacturing' => 'Manufacturing', 'finance' => 'Finance', 'order-fulfillment' => 'Order Fulfillment', 'ecommerce' => 'E-commerce', 'bi' => 'Business Intelligence', 'client.itsm' => 'ITSM'] as $prefix => $department) {
            if (str_starts_with($route, $prefix.'.')) return $department;
        }

        return 'ITSM';
    }
}
