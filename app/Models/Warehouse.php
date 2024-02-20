<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\CompanyFilter;
    use Traits\ModelTable;

    protected $fillable = [
      'warehouse_name',
      'warehouse_description',
      'location_id',
      'company_id'
    ];
    
    // use Traits\Syncer;

    // a warehouse belongs to one company
    public function systemuser()
    {
      return $this->belongsTo(Company::class);
    }

    // a warehouse belongs to one location
    public function location()
    {
      return $this->belongsTo(Location::class);
    }

    // a warehouse belongs to one location
    public function floors()
    {
      return $this->hasMany(Floor::class);
    }
}
