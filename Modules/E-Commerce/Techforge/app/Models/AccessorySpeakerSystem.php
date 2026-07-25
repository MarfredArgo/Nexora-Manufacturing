<?php

namespace Modules\Ecommerce\Models;

use Modules\Ecommerce\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessorySpeakerSystem extends Model
{
    use BelongsToClient;
    use HasFactory;
    
    protected $table = 'accessories_speaker_systems';
    protected $guarded = [];
}
