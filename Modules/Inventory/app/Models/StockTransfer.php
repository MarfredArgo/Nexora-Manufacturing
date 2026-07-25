<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    use BelongsToClient;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'item_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'approved_by');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function getReferenceAttribute(): string
    {
        return 'TRF-' . $this->created_at->format('Y') . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}


