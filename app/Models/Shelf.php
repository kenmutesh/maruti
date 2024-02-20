<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shelf extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;
    // use Traits\Syncer;

    protected $fillable = [
      'shelf_name',
      'floor_id',
      'company_id'
    ];

    // a shelf belongs to one company
    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    // a shelf belongs to one floor
    public function floor()
    {
      return $this->belongsTo(Floor::class);
    }

    // a shelf can have many bins
    public function bins()
    {
      return $this->hasMany(Bin::class);
    }

    // getting last one - mostly used in API
    public function getLastShelfInfo()
    {
      return $this->orderBy('date_created', 'desc')->with('floor')->first();
    }

}
