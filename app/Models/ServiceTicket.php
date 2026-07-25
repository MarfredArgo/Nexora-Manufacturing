<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_id',
    'created_by',
    'ticket_no',
    'ticket_type',
    'requester',
    'client_name',
    'module',
    'category',
    'priority',
    'status',
    'subject',
    'description',
])]
class ServiceTicket extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
