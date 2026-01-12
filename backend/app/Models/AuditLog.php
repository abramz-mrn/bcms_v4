<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id','user_name','ip_address','action','resource_type','old_value','new_value','description'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];
}