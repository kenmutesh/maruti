<?php

namespace App\Models;

use App\Enums\DocumentLabelsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierCreditNote extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    use Traits\CompanyFilter;

    protected $fillable = [
        'supplier_credit_note_prefix',
        'supplier_credit_note_suffix',
        'supplier_id',
        'purchase_order_id',
        'record_date',
        'memo',
        'sum_subtotal',
        'sum_vataddition',
        'sum_grandtotal',
        'cancelled_at',
        'company_id',
    ];

    public function creditnoteitems()
    {
        return $this->hasMany(SupplierCreditNoteItem::class, 'supplier_credit_note_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseorder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function getNextCreditNotePrefixAttribute()
    {
        $documentLabel = DocumentLabel::select('document_prefix')->where([
            'document' => DocumentLabelsEnum::SUPPLIERCREDITNOTE->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        return $documentLabel->document_prefix;
    }

    public function getNextCreditNoteSuffixAttribute()
    {
        $documentLabel = DocumentLabel::select('document_suffix')->where([
            'document' => DocumentLabelsEnum::SUPPLIERCREDITNOTE->value,
            'company_id' => auth()->user()->company_id
        ])->first();

        $lastSuffix = SupplierCreditNote::where([
            ['supplier_credit_note_suffix', '>=', $documentLabel->document_suffix],
            ['company_id', '=', auth()->user()->company_id]
        ])->max('supplier_credit_note_suffix');

        if ($lastSuffix != null) {
            return $lastSuffix + 1;
        }
        return $documentLabel->document_suffix + 1;
    }

    public function getSubTotalAttribute()
    {
        $subTotal = 0;

        $creditNoteItems = $this->creditnoteitems->toArray();
        
        $creditNoteItemsSubTotal = array_reduce($creditNoteItems, function ($accumulator, $item) {
            return $accumulator + $item['sub_total'];
        }, 0);

        $subTotal += $creditNoteItemsSubTotal;

        return round($subTotal, 2);
    }

    public function getVatAdditionAttribute()
    {
        $subTotal = 0;

        $creditNoteItems = $this->creditnoteitems->toArray();

        $creditNoteItemsVatAddition = array_reduce($creditNoteItems, function ($accumulator, $item) {
            return $accumulator + ($item['vat_addition'] * $item['quantity']);
        }, 0);

        $subTotal += $creditNoteItemsVatAddition;

        return round($subTotal, 2);
    }

    public function getGrandTotalAttribute()
    {
        $total = $this->sub_total + $this->vat_addition;
        return round($total);
    }

    public function updateAmounts()
    {
        $this->updateQuietly([
            'sum_subtotal' => $this->sub_total,
            'sum_vataddition' => $this->vat_addition,
            'sum_grandtotal' => $this->grand_total
        ]);
    }
}
