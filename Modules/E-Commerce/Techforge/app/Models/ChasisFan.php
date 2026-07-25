<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class ChasisFan extends Model
{
    use BelongsToClient;
    protected $table = 'components_chasisfan';

    protected $fillable = [
        'name', 'price', 'size', 'rpm', 'airflow', 'noise_level', 'color', 'rgb', 'image_url'
    , 'brand'];
}
