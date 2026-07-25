<?php

namespace Modules\OrderFulfillment\Models;

use Modules\OrderFulfillment\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use BelongsToClient;
    // Table name doesn't match the model name, so specify it explicitly
    protected $table = 'requisitions';

    protected $fillable = [
        'req_number',
        'item',
        'qty',
        'department',
        'requested_by',
        'date_requested',
        'notes',
        'priority',
        'categories',
    ];

    protected $casts = [
        'date_requested' => 'date',
        'qty' => 'integer',
    ];
}



