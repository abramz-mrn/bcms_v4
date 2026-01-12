<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number','customers_id','products_id','caller_name','phone','email',
        'category','priority','subject','description','status',
        'assigned_to','assigned_at','resolved_at','closed_at','sla_due_date',
        'resolution_notes','customer_rating','customer_feedback'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_due_date' => 'datetime',
    ];
}