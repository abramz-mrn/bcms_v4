<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $fillable = [
        'user_group_id','company_id','name','password','nik','photo','phone','email','locked'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }
}