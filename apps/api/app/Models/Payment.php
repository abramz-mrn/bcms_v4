<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoices_id','payment_method','payment_gateway','transaction_id','amount','fee','paid_at',
        'reference_number','document_proof','status','notes','created_by'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
    ];
}