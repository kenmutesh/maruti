<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCCEmail extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    // use Traits\Syncer;

    protected $fillable = [
        'customer_id',
        'email',
    ];
}
