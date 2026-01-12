<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = ['products_id','name','description','start_date','end_date','discount'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount' => 'decimal:2',
    ];
}