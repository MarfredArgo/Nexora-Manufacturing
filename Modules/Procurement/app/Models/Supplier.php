<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $connection = 'procurement';
    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'address',
        'badge_color', 'status', 'brand', 'product_items',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function products()
    {
        return $this->hasMany(SupplierProduct::class);
    }
}