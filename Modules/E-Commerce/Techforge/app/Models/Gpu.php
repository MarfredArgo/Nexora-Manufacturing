<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Gpu extends Model
{
    use BelongsToClient;
    protected $table = 'components_gpus';
    
    protected $fillable = [
        'name', 'price', 'tdp', 'length_mm', 'chipset', 'memory', 'boost_clock', 'color', 'image_url'
    , 'brand'];
}
