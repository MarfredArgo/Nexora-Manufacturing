<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'description',
        'department', // if you have this column
    ];

    // Example relationship if roles have users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Example if roles have permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

}
