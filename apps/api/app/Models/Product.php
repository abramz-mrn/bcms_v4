<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code','name','type','description','market_segment','billing_cycle','price','tax_rate','tax_included'
    ];

    protected $casts = [
        'tax_included' => 'boolean',
        'price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];
}