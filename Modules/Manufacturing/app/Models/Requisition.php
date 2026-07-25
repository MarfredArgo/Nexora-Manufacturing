<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use BelongsToClient;
protected $fillable   = [
        'req_id', 'part_name', 'quantity', 'department', 'destination',
        'requested_by', 'priority', 'wo_id', 'notes', 'date_requested', 'status',
    ];

    protected $casts = [
        'date_requested' => 'date',
    ];
}
