<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessoryKeyboardAccessory extends Model
{
    use BelongsToClient;
    use HasFactory;
    
    protected $table = 'accessories_keyboard_accessories';
    protected $guarded = [];
}
