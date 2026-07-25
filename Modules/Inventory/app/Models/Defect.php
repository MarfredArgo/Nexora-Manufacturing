<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    use BelongsToClient;

    protected $table = 'defects';

    protected $fillable = [
        'part_name', 'quantity', 'description', 'status',
        'source', 'source_id', 'created_by',
    ];
}
