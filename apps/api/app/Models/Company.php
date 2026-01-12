<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','alias','address','city','state','pos','phone','email','logo','bank_account','npwp'
    ];

    protected $casts = [
        'bank_account' => 'array',
    ];
}