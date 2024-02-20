<?php

namespace App\Models;

use App\Enums\PurchaseOrderDocumentsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDocument extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    // use Traits\Syncer;

    protected $fillable = [
      'purchase_order_id',
      'type',
      'document_path',
      'document_name'
    ];

    protected $casts = [
        'document' => PurchaseOrderDocumentsEnum::class,
    ];
}
