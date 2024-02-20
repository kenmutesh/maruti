<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use Traits\ModelTable;
    use Traits\CompanyFilter;
    use SoftDeletes;

    protected $fillable = [
      'name',
      'privileges',
      'created_by',
      'company_id'
    ];

    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    public function users()
    {
      return $this->hasMany(User::class);
    }

    public function getDecodedPrivilegesAttribute(){
      return json_decode($this->privileges);
    }
}
