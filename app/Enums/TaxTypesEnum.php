<?php

namespace App\Enums;

enum TaxTypesEnum: Int
{
    case VAT = 1;

    public function humanreadablestring(): string
    {
        return match ($this) {
            TaxTypesEnum::VAT => 'Value Added Tax',
        };
    }
}
