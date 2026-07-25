<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class ReworkOrder extends Model
{
    use BelongsToClient;
protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'wo_id', 'build_name', 'assigned_tech', 'raised_by',
        'raised_date', 'status', 'priority', 'notes', 'escalated_to_inventory',
    ];

    protected $casts = [
        'escalated_to_inventory' => 'boolean',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'wo_id');
    }

    public function failedChecks()
    {
        return $this->hasMany(ReworkFailedCheck::class, 'rework_id');
    }

    public function requiredParts()
    {
        return $this->hasMany(ReworkRequiredPart::class, 'rework_id')->orderBy('id');
    }
}
