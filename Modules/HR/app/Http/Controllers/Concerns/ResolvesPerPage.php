<?php

namespace Modules\HR\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait ResolvesPerPage
{
    protected function perPage(Request $request, int $default = 20): int
    {
        $allowed = [20, 50, 70, 100];
        $value = (int) $request->query('per_page', $default);

        return in_array($value, $allowed, true) ? $value : $default;
    }
}
