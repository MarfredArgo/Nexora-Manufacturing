<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use BelongsToClient;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeOutOfStock($query)
    {
        return $query->whereIn('items.id', function ($sq) {
            $sq->select('item_id')
                ->from('stock_levels')
                ->groupBy('item_id')
                ->havingRaw('COALESCE(SUM(stock - reserved_quantity), 0) <= 0');
        });
    }

    public function scopeLowStock($query)
    {
        return $query->whereIn('items.id', function ($sq) {
            $sq->select('item_id')
                ->from('stock_levels')
                ->whereRaw('COALESCE(stock - reserved_quantity, 0) <= reorder_threshold')
                ->whereRaw('COALESCE(stock - reserved_quantity, 0) > 0')
                ->where('reorder_threshold', '>', 0)
                ->groupBy('item_id');
        })->whereIn('items.id', function ($sq) {
            $sq->select('item_id')
                ->from('stock_levels')
                ->groupBy('item_id')
                ->havingRaw('COALESCE(SUM(stock - reserved_quantity), 0) > 0');
        });
    }

    public function scopeInStock($query)
    {
        return $query->whereIn('items.id', function ($sq) {
            $sq->select('item_id')
                ->from('stock_levels')
                ->groupBy('item_id')
                ->havingRaw('COALESCE(SUM(stock - reserved_quantity), 0) > 0');
        })->whereNotIn('items.id', function ($sq) {
            $sq->select('item_id')
                ->from('stock_levels')
                ->whereRaw('COALESCE(stock - reserved_quantity, 0) <= reorder_threshold')
                ->whereRaw('COALESCE(stock - reserved_quantity, 0) > 0')
                ->where('reorder_threshold', '>', 0)
                ->groupBy('item_id');
        });
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stockLevels()->sum('stock');
    }

    public function getTotalAvailableAttribute(): int
    {
        return (int) $this->stockLevels()->sum(DB::raw('COALESCE(stock - reserved_quantity, 0)'));
    }

    public function getStatusAttribute(): string
    {
        $totalAvailable = $this->total_available;

        if ($totalAvailable <= 0) {
            return 'Out of Stock';
        }

        $hasLowStock = $this->stockLevels->contains(fn ($sl) => $sl->status === 'low_stock');

        if ($hasLowStock) {
            return 'Low Stock';
        }

        return 'In Stock';
    }
}


