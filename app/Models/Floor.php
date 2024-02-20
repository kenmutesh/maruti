<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;
    // use Traits\Syncer;

    protected $fillable = [
      'floor_name',
      'warehouse_id',
      'company_id'
    ];

    // a floor belongs to one company
    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    // a floor belongs to one warehouse
    public function warehouse()
    {
      return $this->belongsTo(Warehouse::class);
    }

    // getting last one - mostly used in API
    public function getLastFloorInfo()
    {
      return $this->orderBy('date_created', 'desc')->with('warehouse')->first();
    }

    public function shelves()
    {
      return $this->hasMany(Shelf::class);
    }
}
