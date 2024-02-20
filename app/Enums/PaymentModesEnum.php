<?php

namespace App\Enums;

enum PaymentModesEnum: Int
{
    case CASH = 1;

    case MPESA = 2;

    case KRAWITHHOLDING = 3;

    case RTGS = 4;

    case MANUALADJUSTMENT = 5;

    case MPESAIANDM = 6;

    case CHEQUE = 7;

    public function humanreadablestring(): string
    {
        return match ($this) {
            PaymentModesEnum::CASH => 'Cash',
            PaymentModesEnum::MPESA => 'MPesa',
            PaymentModesEnum::KRAWITHHOLDING => 'KRA Withholding',
            PaymentModesEnum::RTGS => 'RTGS I&M Bank',
            PaymentModesEnum::MANUALADJUSTMENT => 'Manual Adjustment',
            PaymentModesEnum::MPESAIANDM => 'MPESA I&M',
            PaymentModesEnum::CHEQUE => 'Cheque',
        };
    }
}
