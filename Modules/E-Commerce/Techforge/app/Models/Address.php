<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use BelongsToClient;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'region',
        'province',
        'city',
        'barangay',
        'postal_code',
        'detailed_address',
        'latitude',
        'longitude',
        'label',
        'custom_label',
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
