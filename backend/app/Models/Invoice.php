<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_no','customer_id','subscription_id','product_id','period_start','period_end',
        'amount','tax_amount','discount_amount','total_amount','due_date','status','created_by'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function product() { return $this->belongsTo(Product::class); }
}