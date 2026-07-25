<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeliveryDriver extends Model
{
    protected $connection = 'hr';

    public const STATUS_AVAILABLE = 'AVAILABLE';
    public const STATUS_UNAVAILABLE = 'UNAVAILABLE';

    protected $fillable = [
        'client_id',
        'employee_id',
        'courier_provider',
        'vehicle_type',
        'plate_number',
        'is_active',
        'availability',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('client', function (Builder $query): void {
            if ($clientId = (int) session('employee_client_id')) {
                $query->where($query->getModel()->qualifyColumn('client_id'), $clientId);
            }
        });

        static::creating(function (self $driver): void {
            if (! $driver->client_id && ($clientId = (int) session('employee_client_id'))) {
                $driver->client_id = $clientId;
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('availability', self::STATUS_AVAILABLE);
    }

    public function scopeForCourier(Builder $query, string $courier): Builder
    {
        return $query->whereRaw('UPPER(TRIM(courier_provider)) = ?', [self::normalizeCourier($courier)]);
    }

    public static function normalizeCourier(string $courier): string
    {
        $aliases = [
            'JNT' => 'JNT',
            'J&T' => 'JNT',
            'J&T EXPRESS' => 'JNT',
            'JNT EXPRESS' => 'JNT',
            'FLASH' => 'FLASH',
            'FLASH EXPRESS' => 'FLASH',
        ];

        $value = strtoupper(trim($courier));

        return $aliases[$value] ?? $value;
    }
}
