<?php

namespace Modules\HR\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

trait RespondsWithAjaxList
{
    protected function wantsAjaxList(Request $request): bool
    {
        return $request->ajax() || $request->boolean('ajax');
    }

    protected function ajaxListResponse(string $view, array $data = []): Response|View
    {
        return response()->view($view, $data);
    }
}
