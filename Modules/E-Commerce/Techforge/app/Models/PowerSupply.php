<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class PowerSupply extends Model
{
    use BelongsToClient;
    protected $table = 'components_power_supplies';
    
    protected $fillable = [
        'name', 'price', 'wattage', 'form_factor', 'type', 'modular', 'color', 'efficiency', 'image_url'
    , 'brand'];
}
