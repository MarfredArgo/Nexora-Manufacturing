<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'finance';
    protected $table = 'accounts';
    protected $primaryKey = 'account_id';

    protected $fillable = [
        'name',
        'account_type',
        'detail_type',
        'balance',
    ];

    public $timestamps = true;
}
