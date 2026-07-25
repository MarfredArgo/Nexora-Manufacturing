<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStockLevel extends Model
{
    protected $connection = 'inventory';
    protected $table = 'stock_levels';
    public $timestamps = true;
    protected $fillable = ['item_id', 'warehouse_id', 'stock', 'reorder_threshold', 'reserved_quantity', 'client_id'];
}
