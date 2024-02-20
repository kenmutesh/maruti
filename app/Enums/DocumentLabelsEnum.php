<?php

namespace App\Enums;

enum DocumentLabelsEnum: Int
{
    case PURCHASEORDER = 1;

    case COATING = 2;

    case QUOTATION = 3;

    case CASHSALE = 4;

    case EXTCASHSALE = 5;

    case INVOICE = 6;

    case CREDITNOTE = 7;

    case KRACONTROLUNIT = 8;

    case EXTINVOICE = 9;

    case SUPPLIERCREDITNOTE = 10;

    public function humanreadablestring(): string
    {
        return match ($this) {
            DocumentLabelsEnum::PURCHASEORDER => 'Purchase Order',
            DocumentLabelsEnum::COATING => 'Coating Job',
            DocumentLabelsEnum::QUOTATION => 'Quotation',
            DocumentLabelsEnum::CASHSALE => 'Cash Sale',
            DocumentLabelsEnum::EXTCASHSALE => 'External Cash Sale',
            DocumentLabelsEnum::INVOICE => 'Invoice',
            DocumentLabelsEnum::CREDITNOTE => 'Credit Note',
            DocumentLabelsEnum::KRACONTROLUNIT => 'KRA Control Unit',
            DocumentLabelsEnum::EXTINVOICE => 'External Invoice',
            DocumentLabelsEnum::SUPPLIERCREDITNOTE => 'Supplier Credit Note',
        };
    }
}
