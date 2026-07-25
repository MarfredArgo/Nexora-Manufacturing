<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    protected $connection = 'order_fulfillment';

    protected $table = 'orders';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'customer_name',
        'address',
        'status',
        'due_date',
        'client_id',
        'product_name',
        'qty',
        'product_amount'
    ];


    public function items()
    {
        return $this->hasMany(
            orderitems::class,
            'order_id',
            'id'
        );
    }


    public function invoice()
    {
        return $this->hasOne(
            Invoice::class,
            'order_id',
            'id'
        );
    }
}
