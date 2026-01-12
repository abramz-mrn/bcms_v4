<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternetService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id','router_id','profile','rate_limit','limit_at','priority',
        'start_date','due_date','auto_soft_limit','auto_suspend'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];
}