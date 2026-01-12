<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'user_groups';

    protected $fillable = ['name','permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];
}