<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Add the fillable property here
    protected $fillable = [
        'title',
        'category',
        'target_module',
        'author_name',
        'view_count',
        'status',
    ];

    
}

