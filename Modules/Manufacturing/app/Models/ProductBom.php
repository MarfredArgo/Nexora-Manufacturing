<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Manufacturing\Models\Concerns\BelongsToClient;

class ProductBom extends Model
{
    use BelongsToClient;

    protected $table = 'product_boms';

    protected $fillable = ['sku', 'name', 'description', 'status'];

    public function items(): HasMany
    {
        return $this->hasMany(ProductBomItem::class, 'bom_id');
    }
}
