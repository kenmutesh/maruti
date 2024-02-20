<?php

namespace App\Enums;

enum PurchaseOrderStatusEnum: Int
{
    case OPEN = 1;

    case CLOSED = 2;

    case CANCELLED = 3;

    public function humanreadablestrng(): string
    {
        return match ($this) {
            PurchaseOrderStatusEnum::OPEN => 'Open',
            PurchaseOrderStatusEnum::CLOSED => 'Closed',
            PurchaseOrderStatusEnum::CANCELLED => 'Cancelled',
        };
    }
}
