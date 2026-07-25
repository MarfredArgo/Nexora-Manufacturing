<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class orderitems extends Model
{
    protected $connection = 'order_fulfillment';

    protected $table = 'order_items';

    protected $primaryKey = 'id';


    protected $fillable = [
        'order_id',
        'product_name',
        'qty',
        'product_amount'
    ];


    protected $casts = [
        'qty' => 'integer',
        'product_amount' => 'decimal:2'
    ];


    public function order()
    {
        return $this->belongsTo(
            order::class,
            'order_id',
            'id'
        );
    }
}