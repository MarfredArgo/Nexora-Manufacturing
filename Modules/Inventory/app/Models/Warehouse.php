<?php

namespace Modules\Inventory\Models;

use Modules\Inventory\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use BelongsToClient;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'capacity_units',
        'status',
        'last_activity_at',
        'deactivated_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function touchActivity(): void
    {
        $this->forceFill(['last_activity_at' => now()])->save();
    }

    public function getDaysSinceActivityAttribute(): ?int
    {
        if (!$this->last_activity_at) {
            return null;
        }

        // Prevent misleading values if last_activity_at is accidentally in the future.
        $days = (int) $this->last_activity_at->diffInDays(now());
        return max(0, $days);
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getUsedUnitsAttribute(): int
    {
        return $this->stockLevels()->sum('stock') - $this->stockLevels()->sum('reserved_quantity');
    }

    public function getCapacityPercentageAttribute(): int
    {
        if ($this->capacity_units === 0) {
            return 0;
        }

        return (int) round(($this->used_units / $this->capacity_units) * 100);
    }
}


