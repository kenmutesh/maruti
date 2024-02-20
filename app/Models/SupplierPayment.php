<?php

namespace App\Models;

use App\Enums\PaymentModesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;

    protected $fillable = [
        'supplier_id',
        'created_by',
        'payment_mode',
        'transaction_ref',
        'payment_date',
        'nullified_at',
        'sum_purchase_payments',
        'company_id'
    ];

    protected $casts = [
        'payment_mode' => PaymentModesEnum::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchasepayments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function getPaidAmountAttribute()
    {
        $payments = $this->purchasepayments->toArray();

        $paidAmount = array_reduce($payments, function ($accumulator, $payment) {
            return $accumulator += $payment['amount_applied'];
        }, 0);

        return round($paidAmount, 2);
    }

    public function updateAmounts()
    {
        $this->updateQuietly([
            'sum_purchase_payments' => $this->paid_amount,
        ]);
    }
}
