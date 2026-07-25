<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Finance\Models\Concerns\BelongsToClient;
use Modules\Finance\Models\order;

class Invoice extends Model
{
    use BelongsToClient, HasFactory;


    protected $connection = 'finance';

    protected $table = 'invoice';

    protected $primaryKey = 'invoice_id';


    public $timestamps = false;


    protected $fillable = [
        'order_id',
        'nexora_client_id',

        'issue_date',
        'due_date',

        'invoice_amount',
        'discount',
        'shipping_fee',
        'status',
        'payment_status',

        'paid_amount',
        'outstanding_amount',

        'payment_method',
        'payment_details',
        'reference_number',
        'payment_date',
    ];


    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',

        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
    ];


    public function order()
{
    return $this->belongsTo(order::class, 'order_id', 'id');
}
}
