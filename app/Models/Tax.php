<?php

namespace App\Models;

use App\Enums\TaxTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
  use HasFactory;
  use SoftDeletes;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'percentage',
    'type',
    'company_id'
  ];

  protected $casts = [
    'type' => TaxTypesEnum::class,
  ];


  // an vat can only belong to one company
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
