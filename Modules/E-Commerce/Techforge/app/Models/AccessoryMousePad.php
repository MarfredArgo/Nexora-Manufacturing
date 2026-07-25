<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessoryMousePad extends Model
{
    use BelongsToClient;
    use HasFactory;
    
    protected $table = 'accessories_mouse_pads';
    protected $guarded = [];
}
