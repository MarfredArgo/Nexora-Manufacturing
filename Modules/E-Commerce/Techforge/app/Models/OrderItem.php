<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'product_type', 'name', 
        'price', 'quantity', 'configuration'
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
