<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class ReworkRequiredPart extends Model
{
    use BelongsToClient;
protected $fillable = ['rework_id', 'name', 'status', 'eta'];

    public function reworkOrder()
    {
        return $this->belongsTo(ReworkOrder::class, 'rework_id');
    }
}
