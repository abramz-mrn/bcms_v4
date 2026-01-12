<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provisioning extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_id','subscription_no','router_id','device_brand','device_type','device_sn','device_mac',
        'device_conn','pppoe_name','pppoe_password','initial_name','static_ip','static_gateway',
        'activation_date','technisian_name','document_speedtest','technisian_notes','created_by'
    ];

    protected $casts = [
        'activation_date' => 'date',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}