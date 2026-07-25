<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class QcTemplate extends Model
{
    use BelongsToClient;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'build_type', 'category', 'name', 'tool', 'target', 'operator', 'unit'];
}
