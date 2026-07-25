<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingRequisition extends Model
{
    protected $connection = 'manufacturing';
    protected $table = 'requisitions';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['req_id', 'part_name', 'quantity', 'department', 'requested_by', 'priority', 'wo_id', 'notes', 'date_requested', 'status', 'destination'];
}
