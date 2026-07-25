<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $connection = 'inventory';
    protected $table = 'items';
    public $timestamps = true;
    protected $fillable = ['name', 'sku', 'category_id', 'unit_cost', 'client_id'];
}
