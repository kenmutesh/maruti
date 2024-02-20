<?php

namespace App\Enums;

enum InventoryItemsEnum: Int
{
    case HARDWARE = 1;

    case ALUMINIUM = 2;

    public function humanreadablestring(): string
    {
        return match ($this) {
            InventoryItemsEnum::HARDWARE => 'Hardware',
            InventoryItemsEnum::ALUMINIUM => 'Aluminium',
        };
    }
}
