<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $fillable = ['name', 'permissions'];
    protected $casts = [
        'permissions' => 'array',
    ];
}