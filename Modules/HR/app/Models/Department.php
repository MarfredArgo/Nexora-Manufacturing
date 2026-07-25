<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'hr';

    protected $fillable = [
        'department_name',
        'department_code'
    ];
}
