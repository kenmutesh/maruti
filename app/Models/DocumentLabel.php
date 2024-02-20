<?php

namespace App\Models;

use App\Enums\DocumentLabelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLabel extends Model
{
  use HasFactory;
  use Traits\ModelTable;
  use Traits\CompanyFilter;

  protected $fillable = [
    'document',
    'document_prefix',
    'document_suffix',
    'company_id'
  ];

  protected $casts = [
    'document' => DocumentLabelsEnum::class,
  ];

  // a label belongs to one company
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
}
