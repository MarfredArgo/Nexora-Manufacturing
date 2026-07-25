<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'price',
        'stock',
        'image_url',
    ];
}
