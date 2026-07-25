<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use BelongsToClient;
    protected $table = 'gaminglaptops';
    protected $fillable = [
        'name',
        'brand',
        'processor',
        'gpu',
        'ram',
        'storage',
        'display',
        'price',
        'image_url',
        'is_sold_out'
    ];
}
