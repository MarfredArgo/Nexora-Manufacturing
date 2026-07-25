<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StockMovement extends Model
{
    use BelongsToClient;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'item_id',
        'warehouse_id',
        'quantity',
        'reference',
        'reference_id',
        'performed_by',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->created_at === null) {
                $model->created_at = now();
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(\Modules\HR\Models\Employee::class, 'performed_by');
    }
}


