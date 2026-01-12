<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_no','customer_id','product_id','registration_date','installation_address',
        'email_consent','sms_consent','whatsapp_consent','document_sf','document_asf','document_pks',
        'status','created_by'
    ];

    protected $casts = [
        'registration_date' => 'date',
        'email_consent' => 'bool',
        'sms_consent' => 'bool',
        'whatsapp_consent' => 'bool',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}