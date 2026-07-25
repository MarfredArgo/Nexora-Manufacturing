<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use BelongsToClient;
protected $fillable = ['name', 'role', 'notes'];
}
