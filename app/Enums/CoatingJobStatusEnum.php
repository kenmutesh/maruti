<?php

namespace App\Enums;

enum CoatingJobStatusEnum: Int
{
    case OPEN = 1;

    case CLOSED = 2;
    
    case CANCELLED = 3;

    public function humanreadablestrng(): string
    {
        return match ($this) {
            CoatingJobStatusEnum::OPEN => 'Open',
            CoatingJobStatusEnum::CLOSED => 'Closed',
            CoatingJobStatusEnum::CANCELLED => 'Cancelled',
        };
    }
}
