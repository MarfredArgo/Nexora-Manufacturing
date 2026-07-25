<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;

class HrStaff extends Model
{
    protected $connection = 'HR';
    protected $table      = 'staff';
}
