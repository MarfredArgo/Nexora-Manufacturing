<?php

namespace Modules\OrderFulfillment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\OrderFulfillment\Models\Concerns\BelongsToClient;

class ReturnItem extends Model
{
    use HasFactory, BelongsToClient;

    // returns now has a client_id column (added via the
    // add_client_id_to_returns_table migration) and is tenant-scoped like
    // the rest of the module. BelongsToClient supplies both the
    // 'order_fulfillment' connection and the client_id global scope, so
    // the explicit $connection property this model used to declare is no
    // longer needed.
    protected $table = 'returns';

    protected $fillable = [
        'id',
        'order_id',
        'customer_name',
        'product_name',
        'reason',
        'status',
        'resolution',
        'due_date',
        'address',
        'refund_amount'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}