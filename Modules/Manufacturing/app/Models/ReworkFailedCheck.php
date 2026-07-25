<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class ReworkFailedCheck extends Model
{
    use BelongsToClient;
public $timestamps = false;

    protected $fillable = ['rework_id', 'check_id', 'check_name', 'verdict', 'result', 'target', 'reason'];

    public function reworkOrder()
    {
        return $this->belongsTo(ReworkOrder::class, 'rework_id');
    }
}
