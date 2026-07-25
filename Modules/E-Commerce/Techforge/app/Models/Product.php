<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToClient;
    protected $fillable = [
        'name',
        'category',
        'brand',
        'processor',
        'specs',
        'price',
        'rating',
        'image_url',
        'badge',
        'is_sold_out',
        'tag',
        'os',
        'cpu',
        'gpu',
        'ram',
        'storage',
        'forge_points',
        'shipping_status',
        'promo_tag',
        'original_price',
        'motherboard',
        'psu',
        'case',
        'cooler',
    ];
}
