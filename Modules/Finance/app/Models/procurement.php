<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    protected $connection = 'procurement';

    protected $table = 'purchase_orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'requisition_id',
        'item',
        'qty',
        'amount',
        'order_date'
    ];

    protected $casts = [
    'amount' => 'decimal:2',
    'order_date' => 'date',
];
}