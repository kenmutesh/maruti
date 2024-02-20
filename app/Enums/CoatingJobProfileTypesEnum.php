<?php

namespace App\Enums;

enum CoatingJobProfileTypesEnum: Int
{
    case HEAVY = 1;

    case MEDIUM = 2;
    
    case LIGHT = 3;

    case NOTAPPLICABLE = 4;

    public function humanreadablestring(): string
    {
        return match ($this) {
            CoatingJobProfileTypesEnum::HEAVY => 'Heavy',
            CoatingJobProfileTypesEnum::MEDIUM => 'Medium',
            CoatingJobProfileTypesEnum::LIGHT => 'Light',
            CoatingJobProfileTypesEnum::NOTAPPLICABLE => 'N/A',
        };
    }
}
