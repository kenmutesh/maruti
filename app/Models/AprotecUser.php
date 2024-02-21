<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprotecUser extends Model
{
    use HasFactory;
    use Traits\ModelTable;
    // use Traits\Syncer;

    protected $fillable = [
        'email',
        'username',
        'password',
        'reset_token'
    ];

    // an aprotec user can create many companies
    public function companies()
    {
      return $this->hasMany(Company::class);
    }   
}
