<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToClient;

    protected $fillable = ['name'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}


