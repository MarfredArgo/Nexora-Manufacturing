<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $connection = 'procurement';
    protected $fillable = [
        'po_number', 'supplier_id', 'qty', 'amount', 'status', 'priority',
        'order_date', 'expected_delivery_date', 'created_by', 'remarks',
        'item', 'brand', 'unit_price', 'requisition_id',
        'requisition_reference', 'department', 'warehouse_id', 'delivery_address',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}