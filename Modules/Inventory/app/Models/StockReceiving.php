<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReceiving extends Model
{
    use BelongsToClient;

    protected $fillable = [
        'shipment_number',
        'item_id',
        'warehouse_id',
        'quantity',
        'status',
        'processed_by',
        'remarks',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withDefault();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class)->withDefault();
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'processed_by');
    }
}


