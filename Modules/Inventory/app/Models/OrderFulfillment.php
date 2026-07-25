<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    use BelongsToClient;

    protected $table = 'packing_materials';

    protected $fillable = [
        'name',
        'stock_qty',
        'low_stock_threshold',
        'is_box',
        'box_size',
    ];

    protected $casts = [
        'is_box' => 'boolean',
    ];
}


