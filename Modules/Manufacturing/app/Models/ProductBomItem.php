<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Manufacturing\Models\Concerns\BelongsToClient;

class ProductBomItem extends Model
{
    use BelongsToClient;

    protected $table = 'product_bom_items';

    protected $fillable = ['bom_id', 'inventory_item_id', 'item_sku', 'item_name', 'quantity_required'];

    protected $casts = ['quantity_required' => 'integer'];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(ProductBom::class, 'bom_id');
    }
}
