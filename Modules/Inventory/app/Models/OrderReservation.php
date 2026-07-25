<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReservation extends Model
{
    use BelongsToClient;

    protected $fillable = [
        'order_reference',
        'source',
        'item_id',
        'warehouse_id',
        'quantity',
        'status',
        'reserved_at',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}


