<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use BelongsToClient;
protected $primaryKey   = 'id';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = ['id','name','specs','status','due','due_date','source','fulfillment_order_id','assigned','assigned_employee_id','range'];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function parts()
    {
        return $this->hasMany(WorkOrderPart::class, 'wo_id')->orderBy('id');
    }

    public function qcSessions()
    {
        return $this->hasMany(QcSession::class, 'wo_id');
    }

    public function reworkOrders()
    {
        return $this->hasMany(ReworkOrder::class, 'wo_id');
    }
}
