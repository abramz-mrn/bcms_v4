<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'users_id','users_name','ip_address','action','resource_type','old_value','new_value','description'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];
}