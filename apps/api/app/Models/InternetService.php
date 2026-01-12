<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternetService extends Model
{
    use SoftDeletes;

    protected $table = 'internet_services';

    protected $fillable = [
        'products_id','routers_id','profile','rate_limit','limit_at','priority',
        'start_date','due_date','auto_soft_limit','auto_suspend'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'auto_soft_limit' => 'integer',
        'auto_suspend' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function router()
    {
        return $this->belongsTo(Router::class, 'routers_id');
    }
}