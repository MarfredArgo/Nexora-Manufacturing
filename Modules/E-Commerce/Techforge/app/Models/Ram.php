<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Ram extends Model
{
    use BelongsToClient;
    protected $table = 'components_rams';
    
    protected $fillable = [
        'name', 'price', 'generation', 'capacity', 'speed', 'modules', 'image_url'
    , 'brand'];
}
