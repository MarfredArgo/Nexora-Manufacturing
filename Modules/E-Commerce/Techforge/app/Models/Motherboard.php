<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Motherboard extends Model
{
    use BelongsToClient;
    protected $table = 'components_motherboards';
    
    protected $fillable = [
        'name', 'price', 'socket', 'form_factor', 'supported_ram_gen', 'memory_max', 'memory_slots', 'color', 'image_url', 'brand', 'wifi'
    ];
}
