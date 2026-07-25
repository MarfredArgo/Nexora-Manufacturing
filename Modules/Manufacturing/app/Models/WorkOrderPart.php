<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    use BelongsToClient;
protected $fillable   = ['wo_id','product_id','name','category','status'];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'wo_id');
    }
}
