<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Cpu extends Model
{
    use BelongsToClient;
    protected $table = 'components_cpus';

    protected $fillable = [
        'name',
        'price',
        'socket',
        'tdp',
        'core_count',
        'core_clock',
        'boost_clock',
        'microarchitecture',
        'integrated_graphics',
        'image_url',
        'brand'
    ];
}
