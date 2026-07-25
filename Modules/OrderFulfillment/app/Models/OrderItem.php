<?php

namespace Modules\OrderFulfillment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\OrderFulfillment\Models\Concerns\BelongsToClient;

class OrderItem extends Model
{
    use HasFactory, BelongsToClient;

    // order_items now has a client_id column (added via the
    // add_client_id_to_order_items_table migration) and is tenant-scoped
    // like the rest of the module. BelongsToClient supplies both the
    // 'order_fulfillment' connection and the client_id global scope, so
    // the explicit $connection property this model used to declare is no
    // longer needed.
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_name',
        'qty',
        'product_amount',
    ];

    protected $casts = [
        'qty' => 'integer',
        'product_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function getLineTotalAttribute()
    {
        return $this->qty * $this->product_amount;
    }
}