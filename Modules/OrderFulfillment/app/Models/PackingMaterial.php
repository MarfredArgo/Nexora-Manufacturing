<?php

namespace Modules\OrderFulfillment\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterial extends Model
{
    /**
     * packing_materials lives on the separate "inventory" Neon project,
     * not the default/orders connection. This name must match a
     * connection defined in config/database.php.
     */
    protected $connection = 'inventory';

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