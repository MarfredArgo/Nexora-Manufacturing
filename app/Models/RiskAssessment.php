<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_id',
    'title',
    'category',
    'level',
    'owner',
    'status',
    'review_date',
    'mitigation_plan',
])]
class RiskAssessment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
        ];
    }
}
