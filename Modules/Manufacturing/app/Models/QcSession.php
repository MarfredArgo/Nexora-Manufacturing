<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class QcSession extends Model
{
    use BelongsToClient;
protected $fillable = ['wo_id', 'build_type', 'tech'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'wo_id');
    }

    public function results()
    {
        return $this->hasMany(QcResult::class, 'session_id');
    }
}
