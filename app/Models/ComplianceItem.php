<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_id',
    'title',
    'audience',
    'status',
    'progress',
    'due_date',
    'notes',
])]
class ComplianceItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'progress' => 'integer',
        ];
    }
}
