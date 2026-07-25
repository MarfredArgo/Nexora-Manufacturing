<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_type',
        'name',
        'quantity',
        'price',
        'image_url',
        'configuration',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
