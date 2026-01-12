<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','location','description','ip_address','api_port','ssh_port',
        'api_username','api_password','api_certificate','tls_enabled','ssh_enabled',
        'status','sync_interval','last_sync_at','config_backup'
    ];

    protected $casts = [
        'tls_enabled' => 'boolean',
        'ssh_enabled' => 'boolean',
        'last_sync_at' => 'datetime',
        'config_backup' => 'array',
    ];
}