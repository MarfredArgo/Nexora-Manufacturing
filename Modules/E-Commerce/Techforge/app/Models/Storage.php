<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use BelongsToClient;
    protected $table = 'components_storages';

    protected $fillable = [
        'name', 'price', 'type', 'capacity', 'cache', 'form_factor', 'interface', 'image_url'
    , 'brand'];
}
