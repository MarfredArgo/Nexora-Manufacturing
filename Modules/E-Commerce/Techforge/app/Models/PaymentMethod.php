<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use BelongsToClient;
    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'account_name',
        'account_number_mask',
        'expiry_date',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
