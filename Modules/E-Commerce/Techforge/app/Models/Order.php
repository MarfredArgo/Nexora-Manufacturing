<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'user_id', 'status', 'total', 'shipping_fee', 
        'payment_method', 'payment_status', 'shipping_address', 'tracking_number'
    ];

    protected $casts = [
        'shipping_address' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
