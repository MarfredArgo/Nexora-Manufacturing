<?php

namespace Modules\Manufacturing\Models;

use Modules\Manufacturing\Models\Concerns\BelongsToClient;

use Illuminate\Database\Eloquent\Model;

class QcResult extends Model
{
    use BelongsToClient;
protected $fillable = ['session_id', 'check_id', 'value', 'verdict', 'note'];

    public function session()
    {
        return $this->belongsTo(QcSession::class, 'session_id');
    }

    public function check()
    {
        return $this->belongsTo(QcTemplate::class, 'check_id');
    }
}
