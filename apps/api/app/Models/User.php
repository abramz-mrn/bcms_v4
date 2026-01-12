<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_groups_id',
        'companies_id',
        'nik',
        'photo',
        'phone',
        'locked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'user_groups_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }
}