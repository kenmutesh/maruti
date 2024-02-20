<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bin extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;
    // use Traits\Syncer;

    protected $fillable = [
      'bin_name',
      'bin_description',
      'shelf_id',
      'company_id'
    ];

    // a bin belongs to one company
    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    // a bin belongs to one shelf
    public function shelf()
    {
      return $this->belongsTo(Shelf::class);
    }

    // getting last one - mostly used in API
    public function getLastBinInfo()
    {
      return $this->orderBy('date_created', 'desc')->with('shelf')->first();
    }
}
