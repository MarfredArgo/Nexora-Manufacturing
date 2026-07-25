<?php

return [
    // Temporary QA switch: permits authenticated root admins to open module
    // routes without impersonating a client employee. It defaults to enabled
    // during the integration phase; set the variable to false to turn it off.
    'root_admin_module_testing' => env('ROOT_ADMIN_MODULE_TESTING', true),

    // Optional comma-separated client IDs whose BI dashboards should be
    // precomputed by the scheduler, e.g. BI_WARM_CLIENTS=1,24.
    'bi_warm_clients' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('BI_WARM_CLIENTS', ''))
    ))),
];
