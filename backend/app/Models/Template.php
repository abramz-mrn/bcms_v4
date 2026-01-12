<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','type','subject','content','variables','is_active','created_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'bool',
    ];
}