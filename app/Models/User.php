<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    use Traits\ModelTable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'email_verified_at',
        'company_id',
        'reset_token',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
      return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
