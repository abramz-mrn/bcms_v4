<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customers_id','products_id','registration_date','email_consent','sms_consent','whatsapp_consent',
        'document_sf','document_asf','document_pks','status','created_by'
    ];

    protected $casts = [
        'registration_date' => 'date',
        'email_consent' => 'boolean',
        'sms_consent' => 'boolean',
        'whatsapp_consent' => 'boolean',
    ];
}