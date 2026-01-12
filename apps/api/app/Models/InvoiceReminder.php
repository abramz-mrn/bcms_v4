<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceReminder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoices_id',
        'channel',
        'day_offset',
        'scheduled_for',
        'sent_at',
        'status',
        'message',
        'meta',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'sent_at' => 'datetime',
        'meta' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoices_id');
    }
}