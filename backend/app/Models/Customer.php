<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code','name','id_card_number','address','city','state','pos','group_area',
        'phone','email','document_id_card','notes','created_by'
    ];
}