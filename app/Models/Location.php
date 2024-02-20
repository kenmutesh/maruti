<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;
    // use Traits\Syncer;

    protected $fillable = [
        'location_name',
        'location_description',
        'company_id'
    ];

    // a location belongs to one company
    public function company()
    {
      return $this->belongsTo(Aprotec::class);
    }

    // a location is only created by one system user
    public function systemuser()
    {
      return $this->belongsTo(SystemUser::class);
    }

    // a location can have many warehouses
    public function warehouses()
    {
      return $this->hasMany(Warehouse::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($location) { // before delete() method call this
             $location->warehouses()->delete();
             // do the rest of the cleanup...
        });
    }
}
