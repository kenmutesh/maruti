<?php

namespace App\Enums;

enum PowderAndInventoryLogsEnum: Int
{
    case PURCHASEORDER = 1;

    case COATINGJOB = 2;

    case CREATING = 3;

    case MANUALADJUSMENT = 4;

    case DIRECTINVOICE = 5;

    case DIRECTCASHALE = 6;

    public function humanreadablestring(): string
    {
        return match ($this) {
            PowderAndInventoryLogsEnum::PURCHASEORDER => 'Purchase Order',
            PowderAndInventoryLogsEnum::COATINGJOB => 'Coating Job',
            PowderAndInventoryLogsEnum::CREATING => 'Creating Item',
            PowderAndInventoryLogsEnum::MANUALADJUSMENT => 'Manual Adjustment',
            PowderAndInventoryLogsEnum::DIRECTINVOICE => 'Direct Invoice',
            PowderAndInventoryLogsEnum::DIRECTCASHALE => 'Direct Cash Sale',
        };
    }
}
