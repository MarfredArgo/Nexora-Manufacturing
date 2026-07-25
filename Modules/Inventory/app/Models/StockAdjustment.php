<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use BelongsToClient;

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'type',
        'quantity',
        'reason',
        'status',
        'requested_by',
        'approved_by',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'approved_by');
    }
}


